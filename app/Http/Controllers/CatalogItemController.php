<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CatalogCategory;
use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Empresa;
use App\Models\VehicleBrand;
use App\Models\VehicleType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogItemController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));
        $selectedTypeId = (int) $request->query('catalog_type_id', 0);
        $selectedCategoryId = (int) $request->query('catalog_category_id', 0);
        $selectedType = null;
        $selectedCategory = null;
        $baseQuery = CatalogItem::query()->where('empresa_id', $empresa->id);

        if ($selectedTypeId > 0) {
            $selectedType = CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->find($selectedTypeId);

            if ($selectedType) {
                $baseQuery->where('catalog_type_id', $selectedType->id);
            }
        }

        if ($selectedCategoryId > 0) {
            $selectedCategory = CatalogCategory::query()
                ->where('empresa_id', $empresa->id)
                ->with('type')
                ->find($selectedCategoryId);

            if ($selectedCategory) {
                $baseQuery->where('catalog_category_id', $selectedCategory->id);
                $selectedType = $selectedCategory->type;
            }
        }

        if (!$selectedType) {
            NotificationHelper::error('Primero ingresa a un negocio para ver sus productos o servicios.');

            return redirect()->route('admin.catalog.index');
        }

        $items = (clone $baseQuery)
            ->with(['type', 'category'])
            ->withCount(['variants'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('type', function ($typeQuery) use ($search) {
                            $typeQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('active', true)->count(),
            'purchasable' => (clone $baseQuery)->where('purchasable', true)->count(),
            'reservable' => (clone $baseQuery)->where('reservable', true)->count(),
        ];

        return view('admin.catalog.items.index', compact('empresa', 'items', 'stats', 'selectedType', 'selectedCategory'));
    }

    public function create(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $selectedTypeId = (int) $request->query('catalog_type_id', old('catalog_type_id', 0));
        $selectedCategoryId = (int) $request->query('catalog_category_id', old('catalog_category_id', 0));
        $fromInventory = $request->boolean('inventory');
        $selectedType = null;
        $selectedCategory = null;

        if ($selectedCategoryId > 0 && $selectedTypeId === 0) {
            $selectedCategory = CatalogCategory::query()
                ->where('empresa_id', $empresa->id)
                ->with('type')
                ->find($selectedCategoryId);

            if ($selectedCategory) {
                $selectedTypeId = (int) $selectedCategory->catalog_type_id;
                $selectedType = $selectedCategory->type;
            }
        }

        if ($selectedTypeId > 0 && !$selectedType) {
            $selectedType = CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->find($selectedTypeId);
        }

        if ($fromInventory && (!$selectedType || !$this->isProductBusiness($selectedType))) {
            $selectedType = CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS)
                ->ordered()
                ->first();

            $selectedTypeId = (int) ($selectedType?->id ?? 0);
            $selectedCategoryId = 0;
            $selectedCategory = null;
        }

        $restrictToSelectedType = $selectedType && !$fromInventory;

        $types = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->when($fromInventory, function ($query) {
                $query->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->when($restrictToSelectedType, fn ($query) => $query->whereKey($selectedType->id))
            ->ordered()
            ->get();

        $categories = CatalogCategory::query()
            ->where('empresa_id', $empresa->id)
            ->when($fromInventory, function ($query) use ($types) {
                $query->whereIn('catalog_type_id', $types->pluck('id'));
            })
            ->when(!$fromInventory && $selectedType, fn ($query) => $query->where('catalog_type_id', $selectedType->id))
            ->with('type')
            ->ordered()
            ->get();
        $returnToType = (bool) $request->boolean('return_to_type', $selectedTypeId > 0);
        $vehicleTypes = VehicleType::query()->where('active', true)->ordered()->get();
        $brands = VehicleBrand::query()->where('active', true)->orderBy('name')->get(['id', 'name']);
        $supplyVariants = $this->getSupplyVariants($empresa->id);

        $view = $fromInventory || ($selectedType && $this->isProductBusiness($selectedType))
            ? 'admin.catalog.items.create-product'
            : 'admin.catalog.items.create-service';

        return view($view, compact(
            'empresa',
            'types',
            'categories',
            'selectedTypeId',
            'selectedCategoryId',
            'selectedType',
            'selectedCategory',
            'returnToType',
            'fromInventory',
            'vehicleTypes',
            'brands',
            'supplyVariants'
        ));
    }

    public function store(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();

        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'catalog_category_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'purchasable' => ['nullable', 'boolean'],
            'reservable' => ['nullable', 'boolean'],
            'uses_inventory' => ['nullable', 'boolean'],
            'redirect_to_inventory' => ['nullable', 'boolean'],
            'create_presentation' => ['nullable', 'boolean'],
            'variant_name' => ['nullable', 'string', 'max:255'],
            'variant_presentation' => ['nullable', 'string', 'max:255'],
            'variant_specification' => ['nullable', 'string', 'max:255'],
            'variant_sku' => ['nullable', 'string', 'max:255'],
            'variant_price' => ['nullable', 'numeric', 'min:0'],
            'variant_cost_price' => ['nullable', 'numeric', 'min:0'],
            'variant_stock' => ['nullable', 'integer', 'min:0'],
            'variant_min_stock' => ['nullable', 'integer', 'min:0'],
            'vehicle_type_prices' => ['nullable', 'array'],
            'vehicle_type_prices.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vehicle_type_durations' => ['nullable', 'array'],
            'vehicle_type_durations.*' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'supplies' => ['nullable', 'array'],
            'supplies.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'supplies.*.quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999.999'],
            'supplies.*.unit' => ['nullable', 'string', 'max:30'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->findOrFail($data['catalog_type_id']);

        if ($request->boolean('redirect_to_inventory') && !$this->isProductBusiness($type)) {
            NotificationHelper::error('Inventario solo permite crear productos de negocios tipo producto.');
            return redirect()->back()->withInput();
        }

        $category = $this->resolveCategory($empresa->id, $type->id, $data['catalog_category_id'] ?? null);
        $slug = $this->resolveSlug($empresa->id, $data['name'], $data['slug'] ?? null);

        $behavior = $this->resolveBehaviorForType($type, $request);

        $payload = [
            'empresa_id' => $empresa->id,
            'catalog_type_id' => $type->id,
            'catalog_category_id' => $category?->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'base_price' => $data['base_price'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'active' => $request->boolean('active', true),
            'featured' => $request->boolean('featured'),
            'purchasable' => $behavior['purchasable'],
            'reservable' => $behavior['reservable'],
            'uses_inventory' => $behavior['uses_inventory'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('catalog_items', 'public');
        }

        $item = CatalogItem::create($payload);
        $this->syncVehicleTypePrices($item, $type, $data['vehicle_type_prices'] ?? [], $data['vehicle_type_durations'] ?? []);
        $this->syncSupplies($item, $type, $data['supplies'] ?? []);

        $shouldCreatePresentation = $this->isProductBusiness($type);

        if ($shouldCreatePresentation) {
            CatalogItemVariant::create([
                'catalog_item_id' => $item->id,
                'name' => trim((string) ($data['variant_name'] ?? '')) ?: 'General',
                'presentation' => $this->cleanInput($data['variant_presentation'] ?? null),
                'specification' => $this->cleanInput($data['variant_specification'] ?? null),
                'sku' => $this->cleanInput($data['variant_sku'] ?? null),
                'price' => $data['variant_price'] ?? $item->base_price,
                'cost_price' => $data['variant_cost_price'] ?? null,
                'stock' => $data['variant_stock'] ?? null,
                'min_stock' => (int) ($data['variant_min_stock'] ?? 0),
                'active' => true,
                'is_default' => true,
                'sort_order' => 0,
            ]);
        }

        NotificationHelper::success($this->isProductBusiness($type) ? 'Producto creado correctamente.' : 'Servicio creado correctamente.');

        if ($request->boolean('redirect_to_inventory')) {
            return redirect()->route('admin.inventario.index', ['catalog_type_id' => $type->id]);
        }

        if ($request->boolean('redirect_to_type')) {
            return redirect()->route('admin.catalog-types.show', $item->catalog_type_id);
        }

        return redirect()->route('admin.catalog-items.index', ['catalog_type_id' => $item->catalog_type_id]);
    }

    public function show(CatalogItem $catalogItem)
    {
        $catalogItem->load(['type', 'category', 'variants', 'vehicleTypePrices.vehicleType', 'supplies.variant.item']);

        return view('admin.catalog.items.show', compact('catalogItem'));
    }

    public function edit(CatalogItem $catalogItem)
    {
        $types = CatalogType::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->ordered()
            ->get();
        $categories = CatalogCategory::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->with('type')
            ->ordered()
            ->get();
        $vehicleTypes = VehicleType::query()->where('active', true)->ordered()->get();
        $catalogItem->load(['vehicleTypePrices', 'supplies.variant.item']);
        $supplyVariants = $this->getSupplyVariants($catalogItem->empresa_id);

        return view('admin.catalog.items.edit', compact('catalogItem', 'types', 'categories', 'vehicleTypes', 'supplyVariants'));
    }

    public function update(Request $request, CatalogItem $catalogItem)
    {
        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'catalog_category_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'purchasable' => ['nullable', 'boolean'],
            'reservable' => ['nullable', 'boolean'],
            'uses_inventory' => ['nullable', 'boolean'],
            'vehicle_type_prices' => ['nullable', 'array'],
            'vehicle_type_prices.*' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vehicle_type_durations' => ['nullable', 'array'],
            'vehicle_type_durations.*' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'supplies' => ['nullable', 'array'],
            'supplies.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'supplies.*.quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999.999'],
            'supplies.*.unit' => ['nullable', 'string', 'max:30'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->findOrFail($data['catalog_type_id']);

        $category = $this->resolveCategory($catalogItem->empresa_id, $type->id, $data['catalog_category_id'] ?? null);
        $slug = $this->resolveSlug($catalogItem->empresa_id, $data['name'], $data['slug'] ?? null, $catalogItem->id);

        $behavior = $this->resolveBehaviorForType($type, $request);

        $payload = [
            'catalog_type_id' => $type->id,
            'catalog_category_id' => $category?->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'base_price' => $data['base_price'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'active' => $request->boolean('active'),
            'featured' => $request->boolean('featured'),
            'purchasable' => $behavior['purchasable'],
            'reservable' => $behavior['reservable'],
            'uses_inventory' => $behavior['uses_inventory'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            if ($catalogItem->image && Storage::disk('public')->exists($catalogItem->image)) {
                Storage::disk('public')->delete($catalogItem->image);
            }
            $payload['image'] = $request->file('image')->store('catalog_items', 'public');
        }

        $catalogItem->update($payload);
        $this->syncVehicleTypePrices($catalogItem, $type, $data['vehicle_type_prices'] ?? [], $data['vehicle_type_durations'] ?? []);
        $this->syncSupplies($catalogItem, $type, $data['supplies'] ?? []);

        if ($this->isProductBusiness($type)) {
            $this->ensureDefaultVariant($catalogItem);
        } else {
            $catalogItem->variants()->delete();
        }

        NotificationHelper::success($this->isProductBusiness($type) ? 'Producto actualizado correctamente.' : 'Servicio actualizado correctamente.');

        return redirect()->route('admin.catalog-items.index', ['catalog_type_id' => $catalogItem->catalog_type_id]);
    }

    public function destroy(Request $request, CatalogItem $catalogItem)
    {
        $catalogTypeId = $catalogItem->catalog_type_id;
        $image = $catalogItem->image;

        try {
            $catalogItem->delete();
        } catch (QueryException $exception) {
            NotificationHelper::error('No se puede eliminar este item porque ya tiene movimientos, ventas, compras o paquetes relacionados.');

            if ($request->boolean('return_to_type')) {
                return redirect()->route('admin.catalog-types.show', $catalogTypeId);
            }

            return redirect()->route('admin.catalog-items.index', ['catalog_type_id' => $catalogTypeId]);
        }

        if ($image && Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }

        NotificationHelper::success('Item universal eliminado correctamente.');

        if ($request->boolean('return_to_type')) {
            return redirect()->route('admin.catalog-types.show', $catalogTypeId);
        }

        return redirect()->route('admin.catalog-items.index', ['catalog_type_id' => $catalogTypeId]);
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
    }

    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function resolveCategory(int $empresaId, int $typeId, ?int $categoryId): ?CatalogCategory
    {
        if (!$categoryId) {
            return null;
        }

        return CatalogCategory::query()
            ->where('empresa_id', $empresaId)
            ->where('catalog_type_id', $typeId)
            ->findOrFail($categoryId);
    }

    private function isProductBusiness(CatalogType $type): bool
    {
        return ($type->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_PRODUCTS;
    }

    private function resolveBehaviorForType(CatalogType $type, Request $request): array
    {
        if ($this->isProductBusiness($type)) {
            return [
                'purchasable' => true,
                'reservable' => false,
                'uses_inventory' => true,
            ];
        }

        return [
            'purchasable' => true,
            'reservable' => $request->boolean('reservable'),
            'uses_inventory' => false,
        ];
    }

    private function ensureDefaultVariant(CatalogItem $item): void
    {
        if ($item->variants()->exists()) {
            return;
        }

        CatalogItemVariant::create([
            'catalog_item_id' => $item->id,
            'name' => 'General',
            'price' => $item->base_price,
            'stock' => 0,
            'min_stock' => 0,
            'active' => true,
            'is_default' => true,
            'sort_order' => 0,
        ]);
    }

    private function syncVehicleTypePrices(CatalogItem $item, CatalogType $type, array $prices, array $durations = []): void
    {
        if ($this->isProductBusiness($type)) {
            $item->vehicleTypePrices()->delete();
            return;
        }

        $validTypeIds = VehicleType::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $submittedTypeIds = collect(array_merge(array_keys($prices), array_keys($durations)))
            ->map(fn ($vehicleTypeId) => (int) $vehicleTypeId)
            ->unique()
            ->filter(fn ($vehicleTypeId) => in_array($vehicleTypeId, $validTypeIds, true));

        $normalized = $submittedTypeIds->mapWithKeys(function (int $vehicleTypeId) use ($prices, $durations) {
            $price = $prices[$vehicleTypeId] ?? null;
            $duration = $durations[$vehicleTypeId] ?? null;

            if (($price === null || $price === '') && ($duration === null || $duration === '')) {
                return [];
            }

            return [
                $vehicleTypeId => [
                    'price' => $price === null || $price === '' ? null : (float) $price,
                    'duration_minutes' => $duration === null || $duration === '' ? null : (int) $duration,
                ],
            ];
        });

        $item->vehicleTypePrices()
            ->whereNotIn('vehicle_type_id', $normalized->keys())
            ->delete();

        foreach ($normalized as $vehicleTypeId => $values) {
            $item->vehicleTypePrices()->updateOrCreate(
                ['vehicle_type_id' => $vehicleTypeId],
                $values
            );
        }
    }

    private function syncSupplies(CatalogItem $item, CatalogType $type, array $supplies): void
    {
        if ($this->isProductBusiness($type)) {
            $item->supplies()->delete();
            return;
        }

        $validVariantIds = CatalogItemVariant::query()
            ->whereHas('item.type', fn ($query) => $query->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $normalized = collect($supplies)
            ->map(function ($supply) {
                return [
                    'catalog_item_variant_id' => (int) ($supply['catalog_item_variant_id'] ?? 0),
                    'quantity' => $supply['quantity'] ?? null,
                    'unit' => $this->cleanInput($supply['unit'] ?? null),
                ];
            })
            ->filter(fn ($supply) => in_array($supply['catalog_item_variant_id'], $validVariantIds, true) && $supply['quantity'] !== null && $supply['quantity'] !== '')
            ->mapWithKeys(fn ($supply) => [
                $supply['catalog_item_variant_id'] => [
                    'quantity' => (float) $supply['quantity'],
                    'unit' => $supply['unit'],
                ],
            ]);

        $item->supplies()
            ->whereNotIn('catalog_item_variant_id', $normalized->keys())
            ->delete();

        foreach ($normalized as $variantId => $values) {
            $item->supplies()->updateOrCreate(
                ['catalog_item_variant_id' => $variantId],
                $values
            );
        }
    }

    private function getSupplyVariants(int $empresaId)
    {
        return CatalogItemVariant::query()
            ->with(['item.type'])
            ->whereHas('item', function ($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId)
                    ->where('active', true)
                    ->whereHas('type', fn ($typeQuery) => $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS));
            })
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    private function resolveSlug(int $empresaId, string $name, ?string $slug, ?int $ignoreId = null): ?string
    {
        $base = Str::slug($slug ?: $name);

        if ($base === '') {
            return null;
        }

        $candidate = $base;
        $suffix = 2;

        while (
            CatalogItem::query()
                ->where('empresa_id', $empresaId)
                ->where('slug', $candidate)
                ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}
