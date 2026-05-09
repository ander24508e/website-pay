<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'activeVariants'])->latest()->paginate(10);
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
            'price'       => 'nullable|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.presentation' => 'nullable|string|max:100',
            'variants.*.specification' => 'nullable|string|max:120',
            'variants.*.sku' => 'nullable|string|max:120',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.active' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $request->except('image', 'variants');
                $data['price'] = $data['price'] ?? 0;
                $data['active'] = $request->has('active');

                if ($request->hasFile('image')) {
                    $data['image'] = $request->file('image')->store('products', 'public');
                }

                $product = Product::create($data);
                $this->syncVariants($product, $request->input('variants', []));
            });

            NotificationHelper::success('Producto creado correctamente.');
            return redirect()->route('admin.products.index');

        } catch (\Exception $e) {
            NotificationHelper::error('Error al crear el producto: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load('variants');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('variants');
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
            'price'       => 'nullable|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.name' => 'nullable|string|max:255',
            'variants.*.presentation' => 'nullable|string|max:100',
            'variants.*.specification' => 'nullable|string|max:120',
            'variants.*.sku' => 'nullable|string|max:120',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.active' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                $data = $request->except('image', 'variants');
                $data['price'] = $data['price'] ?? $product->price ?? 0;
                $data['active'] = $request->has('active');

                if ($request->hasFile('image')) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $data['image'] = $request->file('image')->store('products', 'public');
                }

                $product->update($data);
                $this->syncVariants($product, $request->input('variants', []));
            });

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

    private function syncVariants(Product $product, array $variantsInput): void
    {
        $rows = collect($variantsInput)
            ->map(function ($row) {
                return [
                    'id' => $row['id'] ?? null,
                    'name' => trim((string) ($row['name'] ?? '')),
                    'presentation' => trim((string) ($row['presentation'] ?? '')),
                    'specification' => trim((string) ($row['specification'] ?? '')),
                    'sku' => trim((string) ($row['sku'] ?? '')),
                    'price' => $row['price'] ?? null,
                    'stock' => $row['stock'] ?? null,
                    'active' => isset($row['active']) ? (bool) $row['active'] : true,
                ];
            })
            ->filter(fn($row) => $row['name'] !== '' && $row['price'] !== null && $row['price'] !== '')
            ->values();

        if ($rows->isEmpty()) {
            $rows = collect([[
                'name' => 'Presentacion base',
                'presentation' => 'unidad',
                'specification' => null,
                'sku' => null,
                'price' => (float) $product->price,
                'stock' => null,
                'active' => true,
            ]]);
        }

        $existingIds = $product->variants()->pluck('id')->all();
        $keptIds = [];
        $minPrice = null;

        foreach ($rows as $index => $row) {
            $payload = [
                'name' => $row['name'],
                'presentation' => $row['presentation'] ?: null,
                'specification' => $row['specification'] ?: null,
                'sku' => $row['sku'] ?: null,
                'price' => (float) $row['price'],
                'stock' => $row['stock'] !== null && $row['stock'] !== '' ? (int) $row['stock'] : null,
                'active' => (bool) $row['active'],
                'is_default' => false,
            ];

            if ($row['id']) {
                $variant = $product->variants()->whereKey($row['id'])->first();
                if ($variant) {
                    $variant->update($payload);
                    $keptIds[] = $variant->id;
                }
            } else {
                $variant = $product->variants()->create($payload);
                $keptIds[] = $variant->id;
            }

            if ($payload['active']) {
                $minPrice = $minPrice === null ? $payload['price'] : min($minPrice, $payload['price']);
            }

            if ($index === 0 && isset($variant)) {
                $variant->update(['is_default' => true]);
            }
        }

        $deleteIds = array_diff($existingIds, $keptIds);
        if (!empty($deleteIds)) {
            $product->variants()->whereIn('id', $deleteIds)->delete();
        }

        if ($minPrice !== null) {
            $product->update(['price' => $minPrice]);
        }
    }
}
