<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'provider'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
        ]);

        try {
            $data = $request->except('image');
            $data['active'] = $request->has('active');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            Product::create($data);

            NotificationHelper::success('Producto creado correctamente.');
            return redirect()->route('admin.products.index');

        } catch (\Exception $e) {
            NotificationHelper::error('Error al crear el producto: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 'product')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'provider'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
        ]);

        try {
            $data = $request->except('image');
            $data['active'] = $request->has('active');

            if ($request->hasFile('image')) {
                // Elimina imagen anterior si existe
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            NotificationHelper::success('Producto actualizado correctamente.');
            return redirect()->route('admin.products.index');

        } catch (\Exception $e) {
            NotificationHelper::error('Error al actualizar el producto: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();

            NotificationHelper::success('Producto eliminado correctamente.');
            return redirect()->route('admin.products.index');

        } catch (\Exception $e) {
            NotificationHelper::error('Error al eliminar el producto: ' . $e->getMessage());
            return back();
        }
    }
}