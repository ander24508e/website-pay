<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CatalogType;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CatalogTypeController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));
        $baseQuery = CatalogType::query()->where('empresa_id', $empresa->id);

        $types = (clone $baseQuery)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->withCount(['categories', 'items'])
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('active', true)->count(),
            'categories' => \App\Models\CatalogCategory::query()->where('empresa_id', $empresa->id)->count(),
            'items' => \App\Models\CatalogItem::query()->where('empresa_id', $empresa->id)->count(),
        ];

        return view('admin.catalog.types.index', compact('empresa', 'types', 'stats'));
    }

    public function create()
    {
        $empresa = $this->getOrCreateEmpresa();

        return view('admin.catalog.types.create', compact('empresa'));
    }

    public function store(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ]);

        $slug = $this->resolveSlug($empresa->id, $data['name'], $data['slug'] ?? null);

        CatalogType::create([
            'empresa_id' => $empresa->id,
            'name' => trim($data['name']),
            'slug' => $slug,
            'icon' => $this->cleanInput($data['icon'] ?? null),
            'description' => $this->cleanInput($data['description'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active', true),
        ]);

        NotificationHelper::success('Tipo de catalogo creado correctamente.');

        return redirect()->route('admin.catalog-types.index');
    }

    public function show(CatalogType $catalogType)
    {
        $catalogType->loadCount(['categories', 'items']);
        $catalogType->load([
            'categories' => fn ($query) => $query->ordered(),
            'items' => fn ($query) => $query->with(['category'])->withCount('variants')->ordered()->limit(12),
        ]);

        return view('admin.catalog.types.show', compact('catalogType'));
    }

    public function edit(CatalogType $catalogType)
    {
        return view('admin.catalog.types.edit', compact('catalogType'));
    }

    public function update(Request $request, CatalogType $catalogType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ]);

        $slug = $this->resolveSlug($catalogType->empresa_id, $data['name'], $data['slug'] ?? null, $catalogType->id);

        $catalogType->update([
            'name' => trim($data['name']),
            'slug' => $slug,
            'icon' => $this->cleanInput($data['icon'] ?? null),
            'description' => $this->cleanInput($data['description'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => $request->boolean('active'),
        ]);

        NotificationHelper::success('Tipo de catalogo actualizado correctamente.');

        return redirect()->route('admin.catalog-types.index');
    }

    public function destroy(CatalogType $catalogType)
    {
        $catalogType->delete();

        NotificationHelper::success('Tipo de catalogo eliminado correctamente.');

        return redirect()->route('admin.catalog-types.index');
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

    private function resolveSlug(int $empresaId, string $name, ?string $slug, ?int $ignoreId = null): ?string
    {
        $base = Str::slug($slug ?: $name);

        if ($base === '') {
            return null;
        }

        $candidate = $base;
        $suffix = 2;

        while (
            CatalogType::query()
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
