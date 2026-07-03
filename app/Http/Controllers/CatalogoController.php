<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Empresa;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CatalogoController extends Controller
{
    private function isUniversalFilter(string $tipo): bool
    {
        return str_starts_with($tipo, 'tipo:');
    }

    private function extractUniversalFilterSlug(string $tipo): ?string
    {
        if (!$this->isUniversalFilter($tipo)) {
            return null;
        }

        $slug = trim(substr($tipo, 5));

        return $slug !== '' ? $slug : null;
    }

    /**
     * Obtiene los items del catalogo universal con filtros y paginacion.
     */
    private function getCatalogItems(string $tipo = 'todos', string $search = '', int $page = 1, int $perPage = 12): array
    {
        $search = trim($search);
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $universalFilterSlug = $this->extractUniversalFilterSlug($tipo);

        if ($tipo !== 'todos' && !$universalFilterSlug) {
            $tipo = 'todos';
        }

        $catalogoUniversales = collect();

        if (Schema::hasTable('catalog_items') && Schema::hasTable('catalog_types')) {
            $universalesQuery = CatalogItem::query()
                ->select('catalog_items.*')
                ->join('catalog_types', 'catalog_types.id', '=', 'catalog_items.catalog_type_id')
                ->with(['type', 'category', 'activeVariants', 'vehicleTypePrices.vehicleType'])
                ->where('catalog_items.active', true)
                ->when($universalFilterSlug, function ($query) use ($universalFilterSlug) {
                    $query->where('catalog_types.slug', $universalFilterSlug);
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('catalog_items.name', 'LIKE', "%{$search}%")
                            ->orWhere('catalog_items.description', 'LIKE', "%{$search}%")
                            ->orWhere('catalog_types.name', 'LIKE', "%{$search}%")
                            ->orWhereHas('category', fn($cq) => $cq->where('name', 'LIKE', "%{$search}%"));
                    });
                })
                ->orderByRaw('CASE WHEN catalog_types.sort_order > 0 THEN 0 ELSE 1 END')
                ->orderBy('catalog_types.sort_order')
                ->orderBy('catalog_types.name')
                ->orderByRaw('CASE WHEN catalog_items.sort_order > 0 THEN 0 ELSE 1 END')
                ->orderBy('catalog_items.sort_order')
                ->orderBy('catalog_items.name');

            $catalogoUniversales = $universalesQuery->get()->map(function ($item) {
                $stockDisponible = $this->resolveAvailableStock($item);
                $isInventariable = (bool) $item->uses_inventory;
                $vehiclePrices = $item->vehicleTypePrices
                    ->filter(fn ($vehiclePrice) => $vehiclePrice->vehicleType?->active)
                    ->map(fn ($vehiclePrice) => [
                        'vehicle_type_id' => (int) $vehiclePrice->vehicle_type_id,
                        'vehicle_type_name' => $vehiclePrice->vehicleType?->name,
                        'price' => (float) $vehiclePrice->price,
                    ])
                    ->values();
                $basePrice = (float) ($item->base_price ?? $item->display_price);
                $publicPrice = $vehiclePrices->isNotEmpty()
                    ? (float) $vehiclePrices->min('price')
                    : $item->display_price;

                return [
                    'id' => $item->id,
                    'nombre' => $item->name,
                    'descripcion' => $item->description,
                    'precio' => $publicPrice,
                    'precio_base' => $basePrice,
                    'imagen' => $item->image,
                    'categoria' => $item->category->name ?? ($item->type->name ?? 'Catalogo'),
                    'tipo' => 'catalog',
                    'tipo_label' => $item->type->name ?? 'Catalogo',
                    'tipo_descripcion' => $item->type->description ?? null,
                    'business_model' => $item->type->business_model ?? null,
                    'comprable' => (bool) $item->purchasable,
                    'reservable' => (bool) $item->reservable,
                    'inventariable' => $isInventariable,
                    'stock_disponible' => $stockDisponible,
                    'agotado' => $isInventariable && $stockDisponible <= 0,
                    'requiere_tipo_vehiculo' => !$isInventariable && $vehiclePrices->isNotEmpty(),
                    'precios_vehiculo' => $vehiclePrices,
                    'variantes' => $item->activeVariants
                        ->sortBy(function ($variant) {
                            return $variant->is_default ? -1 : $variant->sort_order;
                        })
                        ->values()
                        ->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'presentation' => $variant->presentation,
                                'specification' => $variant->specification,
                                'price' => (float) ($variant->price ?? 0),
                                'stock' => (int) ($variant->stock ?? 0),
                                'is_default' => (bool) $variant->is_default,
                            ];
                        }),
                ];
            });
        }

        $catalogo = $catalogoUniversales->values();

        $total = $catalogo->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $items = $catalogo->forPage($page, $perPage)->values();

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => (int) $page,
                'last_page'    => (int) $lastPage,
                'per_page'     => (int) $perPage,
                'total'        => (int) $total,
            ],
        ];
    }

    private function resolveAvailableStock(CatalogItem $item): int
    {
        if (!$item->uses_inventory) {
            return 9999;
        }

        $availableVariant = $item->activeVariants
            ->filter(fn ($itemVariant) => (int) ($itemVariant->stock ?? 0) > 0)
            ->sortBy(fn ($itemVariant) => sprintf(
                '%d-%05d-%s',
                $itemVariant->is_default ? 0 : 1,
                (int) $itemVariant->sort_order,
                (string) $itemVariant->name
            ))
            ->first();

        return max(0, (int) ($availableVariant?->stock ?? 0));
    }

    private function getCatalogFilters(): array
    {
        $filters = [
            ['value' => 'todos', 'label' => 'Todos'],
        ];

        if (!Schema::hasTable('catalog_types')) {
            return $filters;
        }

        $empresa = Empresa::query()->first();
        if (!$empresa) {
            return $filters;
        }

        $types = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->where('active', true)
            ->ordered()
            ->get(['name', 'slug']);

        foreach ($types as $type) {
            if (!$type->slug) {
                continue;
            }

            $filters[] = [
                'value' => 'tipo:' . $type->slug,
                'label' => $type->name,
            ];
        }

        return $filters;
    }

    /**
     * Muestra la pagina principal del catalogo (carga inicial).
     */
    public function index(Request $request)
    {
        $tipo = (string) $request->get('tipo', 'todos');
        $search = (string) $request->get('search', '');
        $page = (int) $request->get('page', 1);

        $data = $this->getCatalogItems($tipo, $search, $page);
        $empresaQuery = Empresa::query();

        if (Schema::hasTable('landing_banners')) {
            $empresaQuery->with('landingBanners');
        }

        $empresa = $empresaQuery->first() ?? new Empresa();

        return view('website.home', [
            'empresa'    => $empresa,
            'catalogo'   => $data['items'],
            'pagination' => $data['pagination'],
            'catalogFilters' => $this->getCatalogFilters(),
            'tipo'       => $tipo,
            'search'     => $search,
            'customerVehicles' => $this->getCustomerVehicles(),
            'vehicleTypes' => $this->getActiveVehicleTypes(),
        ]);
    }

    /**
     * Endpoint AJAX para busqueda y filtrado (devuelve JSON).
     */
    public function buscar(Request $request)
    {
        $tipo = (string) $request->get('tipo', 'todos');
        $search = (string) $request->get('search', '');
        $page = (int) $request->get('page', 1);

        $data = $this->getCatalogItems($tipo, $search, $page);

        if ($request->ajax()) {
            return response()->json($data);
        }

        return redirect()->route('home', [
            'tipo' => $tipo,
            'search' => $search,
            'page' => $page,
        ]);
    }

    private function getCustomerVehicles()
    {
        if (!auth()->check()) {
            return collect();
        }

        return Vehicle::query()
            ->where('user_id', auth()->id())
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNotNull('vehicle_specification_id')
                    ->orWhereNotNull('vehicle_type_id');
            })
            ->where(function ($query) {
                $query->whereHas('specification.type', fn ($type) => $type->where('active', true))
                    ->orWhereHas('type', fn ($type) => $type->where('active', true));
            })
            ->with([
                'brand:id,name',
                'model:id,name',
                'type:id,name',
                'specification.brand:id,name',
                'specification.model:id,name',
                'specification.type:id,name',
            ])
            ->orderBy('plate')
            ->get()
            ->map(fn ($vehicle) => [
                'id' => (int) $vehicle->id,
                'vehicle_type_id' => (int) $vehicle->resolvedType()?->id,
                'label' => trim(sprintf(
                    '%s - %s %s',
                    $vehicle->plate,
                    $vehicle->resolvedBrand()?->name ?? '',
                    $vehicle->resolvedModel()?->name ?? ''
                )),
                'type_name' => $vehicle->resolvedType()?->name,
            ])
            ->filter(fn ($vehicle) => $vehicle['vehicle_type_id'] > 0)
            ->values();
    }

    private function getActiveVehicleTypes()
    {
        return VehicleType::query()
            ->where('active', true)
            ->ordered()
            ->get(['id', 'name'])
            ->map(fn ($vehicleType) => [
                'id' => (int) $vehicleType->id,
                'name' => $vehicleType->name,
            ]);
    }
}
