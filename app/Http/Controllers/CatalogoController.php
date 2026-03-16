<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('active', true)
            ->when($request->categoria, fn($q) => $q->where('category_id', $request->categoria))
            ->when($request->buscar, fn($q) => $q->where('name', 'like', '%'.$request->buscar.'%'))
            ->latest()->paginate(12);

        $services = Service::with('category')
            ->where('active', true)
            ->when($request->categoria, fn($q) => $q->where('category_id', $request->categoria))
            ->when($request->buscar, fn($q) => $q->where('name', 'like', '%'.$request->buscar.'%'))
            ->latest()->paginate(12);

        $categories = Category::all();

        return view('catalogo.index', compact('products', 'services', 'categories'));
    }

    public function showProduct(Product $product)
    {
        abort_if(!$product->active, 404);
        return view('catalogo.product', compact('product'));
    }

    public function showService(Service $service)
    {
        abort_if(!$service->active, 404);
        return view('catalogo.service', compact('service'));
    }
}