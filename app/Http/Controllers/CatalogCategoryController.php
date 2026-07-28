<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CatalogCategory;
use App\Models\CatalogType;
use App\Models\Empresa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));
        $selectedTypeId = (int) $request->query('catalog_type_id', 0);
        $selectedStatus = (string) $request->query('status', '');
        $selectedType = null;
        $baseQuery = CatalogCategory::query()->where('empresa_id', $empresa->id);

        if ($selectedTypeId > 0) {
            $selectedType = CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->find($selectedTypeId);

            if ($selectedType) {
                $baseQuery->where('catalog_type_id', $selectedType->id);
            }
        }

        $categories = (clone $baseQuery)
            ->with(['type'])
            ->withCount(['items'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('type', function ($typeQuery) use ($search) {
                            $typeQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($selectedStatus, ['active', 'inactive'], true), function ($query) use ($selectedStatus) {
                $query->where('active', $selectedStatus === 'active');
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('active', true)->count(),
            'with_items' => (clone $baseQuery)->has('items')->count(),
            'types' => CatalogType::query()->where('empresa_id', $empresa->id)->count(),
        ];

        $types = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->ordered()
            ->get(['id', 'name']);

        return view('admin.catalog.categories.index', compact('empresa', 'categories', 'stats', 'selectedType', 'types', 'selectedStatus'));
    }

    public function create(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $selectedTypeId = (int) $request->query('catalog_type_id', old('catalog_type_id', 0));
        $selectedType = null;
        $types = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->ordered()
            ->get();

        if ($selectedTypeId > 0) {
            $selectedType = CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->find($selectedTypeId);

            if ($selectedType) {
                $types = collect([$selectedType]);
            }
        }

        $returnToType = (bool) $request->boolean('return_to_type', $selectedTypeId > 0);

        return view('admin.catalog.categories.create', compact('empresa', 'types', 'selectedTypeId', 'selectedType', 'returnToType'));
    }

    public function store(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();

        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'redirect_to_type' => ['nullable', 'boolean'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->findOrFail($data['catalog_type_id']);

        $slug = $this->resolveSlug($type->id, $data['name'], $data['slug'] ?? null);

        $category = CatalogCategory::create([
            'empresa_id' => $empresa->id,
            'catalog_type_id' => $type->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active', true),
        ]);

        NotificationHelper::success('Categoria universal creada correctamente.');

        if ($request->boolean('redirect_to_type')) {
            return redirect()->route('admin.catalog-types.show', $category->catalog_type_id);
        }

        return redirect()->route('admin.catalog-categories.index');
    }

    public function show(CatalogCategory $catalogCategory)
    {
        return redirect()->route('admin.catalog-items.index', [
            'catalog_type_id' => $catalogCategory->catalog_type_id,
            'catalog_category_id' => $catalogCategory->id,
        ]);
    }

    public function edit(CatalogCategory $catalogCategory)
    {
        $types = CatalogType::query()
            ->where('empresa_id', $catalogCategory->empresa_id)
            ->ordered()
            ->get();

        return view('admin.catalog.categories.edit', compact('catalogCategory', 'types'));
    }

    public function update(Request $request, CatalogCategory $catalogCategory)
    {
        $data = $request->validate([
            'catalog_type_id' => ['required', 'integer', 'exists:catalog_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ]);

        $type = CatalogType::query()
            ->where('empresa_id', $catalogCategory->empresa_id)
            ->findOrFail($data['catalog_type_id']);

        $slug = $this->resolveSlug($type->id, $data['name'], $data['slug'] ?? null, $catalogCategory->id);

        $catalogCategory->update([
            'catalog_type_id' => $type->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => $this->cleanInput($data['description'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active'),
        ]);

        NotificationHelper::success('Categoria universal actualizada correctamente.');

        if ($request->boolean('redirect_to_type')) {
            return redirect()->route('admin.catalog-types.show', $catalogCategory->catalog_type_id);
        }

        return redirect()->route('admin.catalog-categories.index');
    }

    public function destroy(Request $request, CatalogCategory $catalogCategory)
    {
        $catalogTypeId = $catalogCategory->catalog_type_id;

        try {
            $catalogCategory->delete();
        } catch (QueryException $exception) {
            NotificationHelper::error('No se puede eliminar esta categoría porque tiene relaciones activas.');

            if ($request->boolean('return_to_type')) {
                return redirect()->route('admin.catalog-types.show', $catalogTypeId);
            }

            return redirect()->route('admin.catalog-categories.index');
        }

        NotificationHelper::success('Categoria universal eliminada correctamente.');

        if ($request->boolean('return_to_type')) {
            return redirect()->route('admin.catalog-types.show', $catalogTypeId);
        }

        return redirect()->route('admin.catalog-categories.index');
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

    private function resolveSlug(int $catalogTypeId, string $name, ?string $slug, ?int $ignoreId = null): ?string
    {
        $base = Str::slug($slug ?: $name);

        if ($base === '') {
            return null;
        }

        $candidate = $base;
        $suffix = 2;

        while (
            CatalogCategory::query()
                ->where('catalog_type_id', $catalogTypeId)
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
