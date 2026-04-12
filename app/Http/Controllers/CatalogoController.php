<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Empresa;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Obtiene los items del catálogo (productos + servicios) con filtros y paginación
     */
    private function getCatalogItems($tipo = 'todos', $search = '', $page = 1, $perPage = 12)
    {
        // Consultas base
        $productosQuery = Product::with('category')->where('active', true);
        $serviciosQuery = Service::with('category')->where('active', true);

        // Aplicar búsqueda por nombre
        if (!empty($search)) {
            $productosQuery->where('name', 'LIKE', "%{$search}%");
            $serviciosQuery->where('name', 'LIKE', "%{$search}%");
        }

        // Filtrar por tipo
        if ($tipo === 'productos') {
            $productos = $productosQuery->latest()->get();
            $servicios = collect();
        } elseif ($tipo === 'servicios') {
            $productos = collect();
            $servicios = $serviciosQuery->latest()->get();
        } else {
            $productos = $productosQuery->latest()->get();
            $servicios = $serviciosQuery->latest()->get();
        }

        // Unificar en una colección con formato consistente
        $catalogo = collect();

        foreach ($productos as $producto) {
            $catalogo->push([
                'id'          => $producto->id,
                'nombre'      => $producto->name,
                'descripcion' => $producto->description,
                'precio'      => $producto->price,
                'imagen'      => $producto->image,
                'categoria'   => $producto->category->name ?? 'Producto',
                'tipo'        => 'producto',
            ]);
        }

        foreach ($servicios as $servicio) {
            $catalogo->push([
                'id'          => $servicio->id,
                'nombre'      => $servicio->name,
                'descripcion' => $servicio->description,
                'precio'      => $servicio->price,
                'imagen'      => $servicio->image,
                'categoria'   => $servicio->category->name ?? 'Servicio',
                'tipo'        => 'servicio',
            ]);
        }

        // Ordenar alfabéticamente (puedes cambiar a precio o fecha)
        $catalogo = $catalogo->sortBy('nombre')->values();

        // Paginación manual (eficiente para conjuntos de hasta miles de registros)
        $total = $catalogo->count();
        $items = $catalogo->forPage($page, $perPage);
        $lastPage = ceil($total / $perPage);

        return [
            'items'      => $items,
            'pagination' => [
                'current_page' => (int) $page,
                'last_page'    => (int) $lastPage,
                'per_page'     => (int) $perPage,
                'total'        => (int) $total,
            ]
        ];
    }

    /**
     * Muestra la página principal del catálogo (carga inicial)
     */
    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'todos');
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $data = $this->getCatalogItems($tipo, $search, $page);
        $empresa = Empresa::first();

        return view('website.home', [
            'empresa'    => $empresa,
            'catalogo'   => $data['items'],
            'pagination' => $data['pagination'],
            'tipo'       => $tipo,
            'search'     => $search,
        ]);
    }

    /**
     * Endpoint AJAX para búsqueda y filtrado (devuelve JSON)
     */
    public function buscar(Request $request)
    {
        $tipo = $request->get('tipo', 'todos');
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $data = $this->getCatalogItems($tipo, $search, $page);

        if ($request->ajax()) {
            return response()->json($data);
        }

        return redirect()->route('home');
    }

    /**
     * Muestra detalle de un producto individual
     */
    public function showProduct(Product $product)
    {
        abort_if(!$product->active, 404);
        return view('catalogo.product', compact('product'));
    }

    /**
     * Muestra detalle de un servicio individual
     */
    public function showService(Service $service)
    {
        abort_if(!$service->active, 404);
        return view('catalogo.service', compact('service'));
    }
}