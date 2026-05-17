<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Empresa;
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
                ->with(['type', 'category', 'activeVariants'])
                ->where('active', true)
                ->when($universalFilterSlug, function ($query) use ($universalFilterSlug) {
                    $query->whereHas('type', function ($typeQuery) use ($universalFilterSlug) {
                        $typeQuery->where('slug', $universalFilterSlug);
                    });
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%")
                            ->orWhereHas('type', fn($tq) => $tq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhereHas('category', fn($cq) => $cq->where('name', 'LIKE', "%{$search}%"));
                    });
                });

            $catalogoUniversales = $universalesQuery->latest()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->name,
                    'descripcion' => $item->description,
                    'precio' => $item->display_price,
                    'imagen' => $item->image,
                    'categoria' => $item->category->name ?? ($item->type->name ?? 'Catalogo'),
                    'tipo' => 'catalog',
                    'tipo_label' => $item->type->name ?? 'Catalogo',
                    'comprable' => (bool) $item->purchasable,
                    'reservable' => (bool) $item->reservable,
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
                                'is_default' => (bool) $variant->is_default,
                            ];
                        }),
                ];
            });
        }

        $catalogo = $catalogoUniversales
            ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

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

    private function getCatalogFilters(): array
    {
        $filters = [
            ['value' => 'todos', 'label' => 'Todos', 'icon' => null],
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
            ->get(['name', 'slug', 'icon']);

        foreach ($types as $type) {
            if (!$type->slug) {
                continue;
            }

            $filters[] = [
                'value' => 'tipo:' . $type->slug,
                'label' => $type->name,
                'icon' => $type->icon,
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
}
