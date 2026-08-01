<?php

namespace App\Livewire\Admin\Catalog;

use App\Helpers\NotificationHelper;
use App\Models\CatalogCategory;
use App\Models\CatalogItem;
use App\Models\CatalogItemSupply;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Empresa;
use App\Models\ServiceVehicleTypePrice;
use App\Models\VehicleType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ServiceForm extends Component
{
    use WithFileUploads;

    public ?int $catalogItemId = null;
    public int $selectedTypeId = 0;
    public $selectedCategoryId = null;
    public string $name = '';
    public string $description = '';
    public bool $active = true;
    public bool $featured = false;
    public bool $reservable = false;
    public $image = null;
    public ?string $currentImage = null;
    public string $returnUrl;
    public bool $showCategoryModal = false;
    public string $newCategoryName = '';
    public string $newCategoryDescription = '';
    public bool $showPricePanel = false;
    public bool $highlightPrices = false;
    public int $highlightKey = 0;
    public string $newVehicleTypeName = '';
    public array $priceRows = [];

    public function mount(
        ?int $catalogItemId = null,
        int $selectedTypeId = 0,
        ?int $selectedCategoryId = null,
        ?string $returnUrl = null
    ): void {
        $this->catalogItemId = $catalogItemId;
        $this->selectedTypeId = $selectedTypeId;
        $this->selectedCategoryId = $selectedCategoryId ?: null;

        if ($catalogItemId) {
            $item = CatalogItem::query()
                ->with(['vehicleTypePrices.supplies'])
                ->findOrFail($catalogItemId);

            $this->selectedTypeId = (int) $item->catalog_type_id;
            $this->selectedCategoryId = $item->catalog_category_id ? (int) $item->catalog_category_id : null;
            $this->name = (string) $item->name;
            $this->description = (string) ($item->description ?? '');
            $this->active = (bool) $item->active;
            $this->featured = (bool) $item->featured;
            $this->reservable = (bool) $item->reservable;
            $this->currentImage = $item->image;
            $this->returnUrl = $returnUrl ?: route('admin.catalog-items.show', $item);
            $this->loadPriceRows($item);
            return;
        }

        $type = $this->selectedTypeId > 0
            ? CatalogType::query()->where('business_model', CatalogType::BUSINESS_MODEL_SERVICES)->find($this->selectedTypeId)
            : null;

        if (!$type) {
            $type = CatalogType::query()
                ->where('business_model', CatalogType::BUSINESS_MODEL_SERVICES)
                ->ordered()
                ->first();
        }

        $this->selectedTypeId = (int) ($type?->id ?? 0);
        $this->returnUrl = $returnUrl ?: ($this->selectedTypeId > 0
            ? route('admin.catalog-items.index', ['catalog_type_id' => $this->selectedTypeId])
            : route('admin.catalog.index'));
    }

    public function save(): void
    {
        $this->selectedCategoryId = $this->selectedCategoryId ?: null;
        $data = $this->validate($this->serviceRules());
        $empresa = $this->getOrCreateEmpresa();
        $type = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->where('business_model', CatalogType::BUSINESS_MODEL_SERVICES)
            ->findOrFail($this->selectedTypeId);

        $category = $this->resolveCategory($empresa->id, $type->id, $this->selectedCategoryId);

        if ($this->cleanInput($this->newCategoryName)) {
            $category = $this->createInlineCategory($empresa->id, $type->id, $this->newCategoryName, $this->newCategoryDescription);
            $this->selectedCategoryId = $category->id;
            $this->newCategoryName = '';
            $this->newCategoryDescription = '';
        }

        $payload = [
            'empresa_id' => $empresa->id,
            'catalog_type_id' => $type->id,
            'catalog_category_id' => $category?->id,
            'name' => trim($data['name']),
            'slug' => $this->resolveSlug($empresa->id, $data['name'], null, $this->catalogItemId),
            'description' => $this->cleanInput($data['description'] ?? null),
            'base_price' => null,
            'duration_minutes' => null,
            'active' => (bool) $data['active'],
            'featured' => (bool) $data['featured'],
            'purchasable' => true,
            'reservable' => (bool) $data['reservable'],
            'uses_inventory' => false,
        ];

        if ($this->image) {
            if ($this->currentImage && Storage::disk('public')->exists($this->currentImage)) {
                Storage::disk('public')->delete($this->currentImage);
            }

            $payload['image'] = $this->image->store('catalog_items', 'public');
        }

        if ($this->catalogItemId) {
            $item = CatalogItem::query()->findOrFail($this->catalogItemId);
            $item->update($payload);
            NotificationHelper::success('Servicio actualizado correctamente.');
        } else {
            $item = CatalogItem::create($payload);
            $this->catalogItemId = $item->id;
            NotificationHelper::success('Servicio guardado correctamente. Ahora puedes agregar sus precios por vehiculo.');
        }

        $this->currentImage = $item->fresh()->image;
        $this->image = null;
        $this->highlightPrices = true;
        $this->highlightKey++;
        $this->dispatch('catalog-service-saved');
    }

    public function openPricePanel(): void
    {
        if (!$this->catalogItemId) {
            return;
        }

        if (empty($this->priceRows)) {
            $this->addPriceRow();
        }

        $this->showPricePanel = true;
    }

    public function closePricePanel(): void
    {
        $this->showPricePanel = false;
    }

    public function addPriceRow(): void
    {
        $usedTypeIds = collect($this->priceRows)->pluck('vehicle_type_id')->filter()->map(fn ($id) => (int) $id)->all();
        $vehicleType = $this->vehicleTypes()->first(fn ($type) => !in_array((int) $type->id, $usedTypeIds, true));

        $this->priceRows[] = [
            'id' => null,
            'vehicle_type_id' => $vehicleType?->id ? (string) $vehicleType->id : '',
            'price' => '',
            'duration_minutes' => '',
            'description' => '',
            'active' => true,
            'supplies' => $this->blankSupplies(),
        ];
    }

    public function removePriceRow(int $index): void
    {
        unset($this->priceRows[$index]);
        $this->priceRows = array_values($this->priceRows);
    }

    public function addVehicleType(): void
    {
        $name = $this->cleanInput($this->newVehicleTypeName);

        if (!$name) {
            $this->addError('newVehicleTypeName', 'Escribe el tipo de vehiculo.');
            return;
        }

        $vehicleType = VehicleType::firstOrCreate(['name' => $name], ['active' => true]);
        $this->newVehicleTypeName = '';
        $this->resetErrorBag('newVehicleTypeName');

        if (empty($this->priceRows)) {
            $this->addPriceRow();
        }

        $lastIndex = array_key_last($this->priceRows);
        $this->priceRows[$lastIndex]['vehicle_type_id'] = (string) $vehicleType->id;
    }

    public function savePriceRows(): void
    {
        if (!$this->catalogItemId) {
            return;
        }

        $this->validate($this->priceRules());

        $item = CatalogItem::query()->findOrFail($this->catalogItemId);
        $keptPriceIds = [];

        foreach ($this->priceRows as $row) {
            $vehicleTypeId = (int) ($row['vehicle_type_id'] ?? 0);
            if ($vehicleTypeId <= 0) {
                continue;
            }

            $price = ServiceVehicleTypePrice::updateOrCreate(
                [
                    'catalog_item_id' => $item->id,
                    'vehicle_type_id' => $vehicleTypeId,
                ],
                [
                    'price' => $row['price'] === '' ? null : (float) $row['price'],
                    'duration_minutes' => $row['duration_minutes'] === '' ? null : (int) $row['duration_minutes'],
                    'description' => $this->cleanInput($row['description'] ?? null),
                    'active' => (bool) ($row['active'] ?? false),
                ]
            );

            $keptPriceIds[] = $price->id;
            $this->syncPriceSupplies($item->id, $price, $row['supplies'] ?? []);
        }

        $item->vehicleTypePrices()
            ->whereNotIn('id', $keptPriceIds)
            ->delete();

        $this->loadPriceRows($item->fresh(['vehicleTypePrices.supplies']));
        $this->showPricePanel = false;
        NotificationHelper::success('Precios por vehiculo guardados correctamente.');
    }

    public function openCategoryModal(): void
    {
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
    }

    public function applyCategory(): void
    {
        if (!$this->cleanInput($this->newCategoryName)) {
            $this->addError('newCategoryName', 'El nombre de la categoria es requerido.');
            return;
        }

        $this->selectedCategoryId = null;
        $this->showCategoryModal = false;
        $this->resetErrorBag('newCategoryName');
    }

    public function clearInlineCategory(): void
    {
        if ($this->selectedCategoryId) {
            $this->newCategoryName = '';
            $this->newCategoryDescription = '';
        }
    }

    public function render()
    {
        $types = $this->serviceTypes();
        $selectedType = $types->firstWhere('id', $this->selectedTypeId);
        $categories = $this->categories();
        $priceCount = $this->catalogItemId
            ? ServiceVehicleTypePrice::query()->where('catalog_item_id', $this->catalogItemId)->count()
            : 0;

        return view('livewire.admin.catalog.service-form', [
            'types' => $types,
            'selectedType' => $selectedType,
            'categories' => $categories,
            'vehicleTypes' => $this->vehicleTypes(),
            'supplyVariants' => $this->supplyVariants(),
            'priceCount' => $priceCount,
        ]);
    }

    private function serviceRules(): array
    {
        return [
            'selectedTypeId' => ['required', 'integer', 'exists:catalog_types,id'],
            'selectedCategoryId' => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'active' => ['boolean'],
            'featured' => ['boolean'],
            'reservable' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    private function priceRules(): array
    {
        return [
            'priceRows' => ['array'],
            'priceRows.*.vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'priceRows.*.price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'priceRows.*.duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'priceRows.*.description' => ['nullable', 'string', 'max:1000'],
            'priceRows.*.active' => ['boolean'],
            'priceRows.*.supplies' => ['array'],
            'priceRows.*.supplies.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'priceRows.*.supplies.*.quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999.999'],
            'priceRows.*.supplies.*.unit' => ['nullable', 'string', 'max:30'],
        ];
    }

    private function loadPriceRows(CatalogItem $item): void
    {
        $this->priceRows = $item->vehicleTypePrices
            ->map(fn (ServiceVehicleTypePrice $price) => [
                'id' => $price->id,
                'vehicle_type_id' => (string) $price->vehicle_type_id,
                'price' => $price->price === null ? '' : (string) $price->price,
                'duration_minutes' => $price->duration_minutes === null ? '' : (string) $price->duration_minutes,
                'description' => (string) ($price->description ?? ''),
                'active' => (bool) $price->active,
                'supplies' => $this->priceSupplies($price),
            ])
            ->values()
            ->all();
    }

    private function priceSupplies(ServiceVehicleTypePrice $price): array
    {
        $rows = $price->supplies
            ->map(fn (CatalogItemSupply $supply) => [
                'catalog_item_variant_id' => $supply->catalog_item_variant_id ? (string) $supply->catalog_item_variant_id : '',
                'quantity' => $supply->quantity === null ? '' : (string) $supply->quantity,
                'unit' => (string) ($supply->unit ?? ''),
            ])
            ->values()
            ->all();

        while (count($rows) < 3) {
            $rows[] = ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''];
        }

        return $rows;
    }

    private function blankSupplies(): array
    {
        return [
            ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
            ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
            ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
        ];
    }

    private function syncPriceSupplies(int $itemId, ServiceVehicleTypePrice $price, array $supplies): void
    {
        $validVariantIds = $this->supplyVariants()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $keptSupplyIds = [];

        foreach ($supplies as $supply) {
            $variantId = (int) ($supply['catalog_item_variant_id'] ?? 0);
            $quantity = $supply['quantity'] ?? null;

            if (!in_array($variantId, $validVariantIds, true) || $quantity === null || $quantity === '') {
                continue;
            }

            $row = CatalogItemSupply::updateOrCreate(
                [
                    'service_vehicle_type_price_id' => $price->id,
                    'catalog_item_variant_id' => $variantId,
                ],
                [
                    'catalog_item_id' => $itemId,
                    'quantity' => (float) $quantity,
                    'unit' => $this->cleanInput($supply['unit'] ?? null),
                ]
            );

            $keptSupplyIds[] = $row->id;
        }

        $price->supplies()
            ->whereNotIn('id', $keptSupplyIds)
            ->delete();
    }

    private function serviceTypes(): Collection
    {
        return CatalogType::query()
            ->where('empresa_id', $this->getOrCreateEmpresa()->id)
            ->where('business_model', CatalogType::BUSINESS_MODEL_SERVICES)
            ->ordered()
            ->get();
    }

    private function vehicleTypes(): Collection
    {
        return VehicleType::query()
            ->where('active', true)
            ->ordered()
            ->get();
    }

    private function categories(): Collection
    {
        if (!$this->selectedTypeId) {
            return collect();
        }

        return CatalogCategory::query()
            ->where('empresa_id', $this->getOrCreateEmpresa()->id)
            ->where('catalog_type_id', $this->selectedTypeId)
            ->ordered()
            ->get();
    }

    private function supplyVariants(): Collection
    {
        return CatalogItemVariant::query()
            ->where('active', true)
            ->whereHas('item.type', fn ($query) => $query->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS))
            ->with('item:id,name')
            ->orderBy('name')
            ->get(['id', 'catalog_item_id', 'name', 'sku', 'active']);
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
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

    private function createInlineCategory(int $empresaId, int $typeId, string $name, ?string $description = null): CatalogCategory
    {
        $name = trim($name);
        $slug = Str::slug($name);
        $candidate = $slug;
        $suffix = 2;

        while (
            $candidate !== ''
            && CatalogCategory::query()
                ->where('catalog_type_id', $typeId)
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $slug . '-' . $suffix;
            $suffix++;
        }

        return CatalogCategory::create([
            'empresa_id' => $empresaId,
            'catalog_type_id' => $typeId,
            'name' => $name,
            'slug' => $candidate ?: null,
            'description' => $this->cleanInput($description),
            'active' => true,
        ]);
    }

    private function resolveSlug(int $empresaId, string $name, ?string $slug, ?int $ignoreId = null): ?string
    {
        $base = Str::slug($this->cleanInput($slug) ?: $name);
        $candidate = $base;
        $suffix = 2;

        while (
            $candidate !== ''
            && CatalogItem::query()
                ->where('empresa_id', $empresaId)
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate ?: null;
    }

    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
