<?php

namespace App\Livewire\Admin\Catalog;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use Livewire\Component;

class TypeItemBrowser extends Component
{
    public int $catalogTypeId;
    public bool $isProductBusiness;
    public string $itemSingular;
    public string $itemSingularTitle;
    public string $itemPlural;
    public string $itemPluralTitle;
    public ?int $selectedItemId = null;

    public function mount(CatalogType $catalogType): void
    {
        $this->catalogTypeId = (int) $catalogType->id;
        $this->isProductBusiness = ($catalogType->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_PRODUCTS;
        $this->itemSingular = $this->isProductBusiness ? 'producto' : 'servicio';
        $this->itemSingularTitle = $this->isProductBusiness ? 'Producto' : 'Servicio';
        $this->itemPlural = $this->isProductBusiness ? 'productos' : 'servicios';
        $this->itemPluralTitle = $this->isProductBusiness ? 'Productos' : 'Servicios';
    }

    public function clearSelection(): void
    {
        $this->selectedItemId = null;
        $this->dispatch('catalog-type-browser-cleared');
    }

    public function render()
    {
        $allItems = $this->baseQuery()
            ->select(['id', 'catalog_category_id', 'name'])
            ->with('category:id,name')
            ->get();

        $itemsQuery = $this->baseQuery()
            ->with('category')
            ->withCount(['variants', 'vehicleTypePrices']);

        if ($this->selectedItemId) {
            $itemsQuery->whereKey($this->selectedItemId);
        } else {
            $itemsQuery->limit(12);
        }

        return view('livewire.admin.catalog.type-item-browser', [
            'allItems' => $allItems,
            'items' => $itemsQuery->get(),
        ]);
    }

    private function baseQuery()
    {
        return CatalogItem::query()
            ->where('catalog_type_id', $this->catalogTypeId)
            ->ordered();
    }
}
