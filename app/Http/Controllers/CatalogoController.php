<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Empresa;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('active', true)
            ->latest()
            ->get();

        $services = Service::with('category')
            ->where('active', true)
            ->latest()
            ->get();

        $categories = Category::all();
        $empresa    = Empresa::first();

        return view('website.home', compact('products', 'services', 'categories', 'empresa'));
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