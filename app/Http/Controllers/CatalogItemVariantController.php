<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Empresa;
use Illuminate\Http\Request;

class CatalogItemVariantController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));
        $baseQuery = CatalogItemVariant::query()
            ->whereHas('item', function ($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id)
                    ->whereHas('type', function ($typeQuery) {
                        $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
                    });
            });

        $variants = (clone $baseQuery)
            ->with(['item.type', 'item.category'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('presentation', 'like', "%{$search}%")
                        ->orWhere('specification', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhereHas('item', function ($itemQuery) use ($search) {
                            $itemQuery->where('name', 'like', "%{$search}%")
                                ->orWhereHas('type', fn($typeQuery) => $typeQuery->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('active', true)->count(),
            'default' => (clone $baseQuery)->where('is_default', true)->count(),
            'with_stock' => (clone $baseQuery)->whereNotNull('stock')->count(),
        ];

        return view('admin.catalog.variants.index', compact('empresa', 'variants', 'stats'));
    }

    public function create(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $selectedItemId = (int) $request->query('catalog_item_id', 0);
        $selectedTypeId = (int) $request->query('catalog_type_id', 0);
        $items = CatalogItem::query()
            ->where('empresa_id', $empresa->id)
            ->whereHas('type', function ($typeQuery) {
                $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->when($selectedItemId > 0, fn ($query) => $query->whereKey($selectedItemId))
            ->when($selectedTypeId > 0, fn ($query) => $query->where('catalog_type_id', $selectedTypeId))
            ->with(['type', 'category'])
            ->ordered()
            ->get();
        $returnToType = (bool) $request->boolean('return_to_type', $selectedTypeId > 0);

        return view('admin.catalog.variants.create', compact('empresa', 'items', 'selectedItemId', 'selectedTypeId', 'returnToType'));
    }

    public function store(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();

        $data = $request->validate([
            'catalog_item_id' => ['required', 'integer', 'exists:catalog_items,id'],
            'name' => ['required', 'string', 'max:255'],
            'presentation' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $item = CatalogItem::query()
            ->where('empresa_id', $empresa->id)
            ->whereHas('type', function ($typeQuery) {
                $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->findOrFail($data['catalog_item_id']);

        $variant = CatalogItemVariant::create([
            'catalog_item_id' => $item->id,
            'name' => trim($data['name']),
            'presentation' => $this->cleanInput($data['presentation'] ?? null),
            'specification' => $this->cleanInput($data['specification'] ?? null),
            'sku' => $this->cleanInput($data['sku'] ?? null),
            'price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        $this->syncDefaultVariant($variant);

        NotificationHelper::success('Presentación de producto creada correctamente.');

        if ($request->boolean('redirect_to_type')) {
            return redirect()->route('admin.catalog-types.show', $item->catalog_type_id);
        }

        return redirect()->route('admin.catalog-variants.index');
    }

    public function show(CatalogItemVariant $catalogVariant)
    {
        $catalogVariant->load(['item.type', 'item.category']);

        if (!$this->isProductVariant($catalogVariant)) {
            NotificationHelper::error('Las presentaciones solo aplican a productos.');
            return redirect()->route('admin.catalog.index');
        }

        return view('admin.catalog.variants.show', compact('catalogVariant'));
    }

    public function edit(CatalogItemVariant $catalogVariant)
    {
        $catalogVariant->load('item.type');

        if (!$this->isProductVariant($catalogVariant)) {
            NotificationHelper::error('Las presentaciones solo aplican a productos.');
            return redirect()->route('admin.catalog.index');
        }

        $items = CatalogItem::query()
            ->where('empresa_id', $catalogVariant->item->empresa_id)
            ->whereHas('type', function ($typeQuery) {
                $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->with(['type', 'category'])
            ->ordered()
            ->get();

        return view('admin.catalog.variants.edit', compact('catalogVariant', 'items'));
    }

    public function update(Request $request, CatalogItemVariant $catalogVariant)
    {
        $catalogVariant->load('item.type');

        if (!$this->isProductVariant($catalogVariant)) {
            NotificationHelper::error('Las presentaciones solo aplican a productos.');
            return redirect()->route('admin.catalog.index');
        }

        $empresaId = $catalogVariant->item->empresa_id;

        $data = $request->validate([
            'catalog_item_id' => ['required', 'integer', 'exists:catalog_items,id'],
            'name' => ['required', 'string', 'max:255'],
            'presentation' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $item = CatalogItem::query()
            ->where('empresa_id', $empresaId)
            ->whereHas('type', function ($typeQuery) {
                $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->findOrFail($data['catalog_item_id']);

        $catalogVariant->update([
            'catalog_item_id' => $item->id,
            'name' => trim($data['name']),
            'presentation' => $this->cleanInput($data['presentation'] ?? null),
            'specification' => $this->cleanInput($data['specification'] ?? null),
            'sku' => $this->cleanInput($data['sku'] ?? null),
            'price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active'),
            'is_default' => $request->boolean('is_default'),
        ]);

        $this->syncDefaultVariant($catalogVariant);

        NotificationHelper::success('Presentación de producto actualizada correctamente.');

        return redirect()->route('admin.catalog-variants.index');
    }

    public function destroy(CatalogItemVariant $catalogVariant)
    {
        $catalogVariant->load('item.type');

        if (!$this->isProductVariant($catalogVariant)) {
            NotificationHelper::error('Las presentaciones solo aplican a productos.');
            return redirect()->route('admin.catalog.index');
        }

        $itemId = $catalogVariant->catalog_item_id;
        $wasDefault = (bool) $catalogVariant->is_default;

        $catalogVariant->delete();

        if ($wasDefault) {
            $replacement = CatalogItemVariant::query()
                ->where('catalog_item_id', $itemId)
                ->ordered()
                ->first();

            if ($replacement) {
                $replacement->update(['is_default' => true]);
            }
        }

        NotificationHelper::success('Presentación de producto eliminada correctamente.');

        return redirect()->route('admin.catalog-variants.index');
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

    private function syncDefaultVariant(CatalogItemVariant $variant): void
    {
        if ($variant->is_default) {
            CatalogItemVariant::query()
                ->where('catalog_item_id', $variant->catalog_item_id)
                ->whereKeyNot($variant->id)
                ->update(['is_default' => false]);

            return;
        }

        $hasDefault = CatalogItemVariant::query()
            ->where('catalog_item_id', $variant->catalog_item_id)
            ->where('is_default', true)
            ->exists();

        if (!$hasDefault) {
            $variant->update(['is_default' => true]);
        }
    }

    private function isProductVariant(CatalogItemVariant $variant): bool
    {
        return ($variant->item?->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_PRODUCTS;
    }
}
