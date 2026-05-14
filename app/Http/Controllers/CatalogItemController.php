<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CatalogCategory;
use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogItemController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));

        $items = CatalogItem::query()
            ->where('empresa_id', $empresa->id)
            ->with(['type', 'category'])
            ->withCount(['variants'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('type', function ($typeQuery) use ($search) {
                            $typeQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.catalog.items.index', compact('empresa', 'items'));
    }

    public function create()
    {
        $empresa = $this->getOrCreateEmpresa();
        $types = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->ordered()
            ->get();
        $categories = CatalogCategory::query()
            ->where('empresa_id', $empresa->id)
            ->with('type')
            ->ordered()
            ->get();

        return view('admin.catalog.items.create', compact('empresa', 'types', 'categories'));
    }

    public function store(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();

        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'catalog_category_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'purchasable' => ['nullable', 'boolean'],
            'reservable' => ['nullable', 'boolean'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->findOrFail($data['catalog_type_id']);

        $category = $this->resolveCategory($empresa->id, $type->id, $data['catalog_category_id'] ?? null);
        $slug = $this->resolveSlug($empresa->id, $data['name'], $data['slug'] ?? null);

        $payload = [
            'empresa_id' => $empresa->id,
            'catalog_type_id' => $type->id,
            'catalog_category_id' => $category?->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'base_price' => $data['base_price'] ?? null,
            'active' => $request->boolean('active', true),
            'featured' => $request->boolean('featured'),
            'purchasable' => $request->boolean('purchasable', true),
            'reservable' => $request->boolean('reservable'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('catalog_items', 'public');
        }

        CatalogItem::create($payload);

        NotificationHelper::success('Item universal creado correctamente.');

        return redirect()->route('admin.catalog-items.index');
    }

    public function show(CatalogItem $catalogItem)
    {
        $catalogItem->load(['type', 'category', 'variants']);

        return view('admin.catalog.items.show', compact('catalogItem'));
    }

    public function edit(CatalogItem $catalogItem)
    {
        $types = CatalogType::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->ordered()
            ->get();
        $categories = CatalogCategory::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->with('type')
            ->ordered()
            ->get();

        return view('admin.catalog.items.edit', compact('catalogItem', 'types', 'categories'));
    }

    public function update(Request $request, CatalogItem $catalogItem)
    {
        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'catalog_category_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'purchasable' => ['nullable', 'boolean'],
            'reservable' => ['nullable', 'boolean'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $catalogItem->empresa_id)
            ->findOrFail($data['catalog_type_id']);

        $category = $this->resolveCategory($catalogItem->empresa_id, $type->id, $data['catalog_category_id'] ?? null);
        $slug = $this->resolveSlug($catalogItem->empresa_id, $data['name'], $data['slug'] ?? null, $catalogItem->id);

        $payload = [
            'catalog_type_id' => $type->id,
            'catalog_category_id' => $category?->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'base_price' => $data['base_price'] ?? null,
            'active' => $request->boolean('active'),
            'featured' => $request->boolean('featured'),
            'purchasable' => $request->boolean('purchasable'),
            'reservable' => $request->boolean('reservable'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->hasFile('image')) {
            if ($catalogItem->image && Storage::disk('public')->exists($catalogItem->image)) {
                Storage::disk('public')->delete($catalogItem->image);
            }
            $payload['image'] = $request->file('image')->store('catalog_items', 'public');
        }

        $catalogItem->update($payload);

        NotificationHelper::success('Item universal actualizado correctamente.');

        return redirect()->route('admin.catalog-items.index');
    }

    public function destroy(CatalogItem $catalogItem)
    {
        if ($catalogItem->image && Storage::disk('public')->exists($catalogItem->image)) {
            Storage::disk('public')->delete($catalogItem->image);
        }

        $catalogItem->delete();

        NotificationHelper::success('Item universal eliminado correctamente.');

        return redirect()->route('admin.catalog-items.index');
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
    }

    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
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

    private function resolveSlug(int $empresaId, string $name, ?string $slug, ?int $ignoreId = null): ?string
    {
        $base = Str::slug($slug ?: $name);

        if ($base === '') {
            return null;
        }

        $candidate = $base;
        $suffix = 2;

        while (
            CatalogItem::query()
                ->where('empresa_id', $empresaId)
                ->where('slug', $candidate)
                ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
}
