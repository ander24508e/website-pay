<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CatalogoController extends Controller
{
    private function isReservableService(Service $service): bool
    {
        $source = strtolower(
            trim(
                ($service->name ?? '') . ' ' .
                ($service->description ?? '') . ' ' .
                ($service->category->name ?? '')
            )
        );

        return str_contains($source, 'lavad');
    }

    /**
     * Obtiene los items del catalogo (productos + servicios) con filtros y paginacion.
     */
    private function getCatalogItems(string $tipo = 'todos', string $search = '', int $page = 1, int $perPage = 12): array
    {
        $tipo = in_array($tipo, ['todos', 'productos', 'servicios'], true) ? $tipo : 'todos';
        $search = trim($search);
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $productosQuery = Product::query()
            ->with(['category', 'activeVariants'])
            ->where('active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('category', fn($cq) => $cq->where('name', 'LIKE', "%{$search}%"));
                });
            });

        $serviciosQuery = Service::query()
            ->with('category')
            ->where('active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('category', fn($cq) => $cq->where('name', 'LIKE', "%{$search}%"));
                });
            });

        $productos = $tipo === 'servicios' ? collect() : $productosQuery->latest()->get();
        $servicios = $tipo === 'productos' ? collect() : $serviciosQuery->latest()->get();

        $catalogoProductos = $productos->map(function ($producto) {
            return [
                'id'          => $producto->id,
                'nombre'      => $producto->name,
                'descripcion' => $producto->description,
                'precio'      => $producto->display_price,
                'imagen'      => $producto->image,
                'categoria'   => $producto->category->name ?? 'Producto',
                'tipo'        => 'product',
            ];
        });

        $catalogoServicios = $servicios->map(function ($servicio) {
            return [
                'id'          => $servicio->id,
                'nombre'      => $servicio->name,
                'descripcion' => $servicio->description,
                'precio'      => $servicio->price,
                'imagen'      => $servicio->image,
                'categoria'   => $servicio->category->name ?? 'Servicio',
                'tipo'        => 'service',
                'reservable'  => $this->isReservableService($servicio),
            ];
        });

        $catalogo = $catalogoProductos
            ->concat($catalogoServicios)
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

    /**
     * Muestra detalle de un producto individual.
     */
    public function showProduct(Product $product)
    {
        abort_if(!$product->active, 404);
        return view('catalogo.product', compact('product'));
    }

    /**
     * Muestra detalle de un servicio individual.
     */
    public function showService(Service $service)
    {
        abort_if(!$service->active, 404);
        return view('catalogo.service', compact('service'));
    }
}
