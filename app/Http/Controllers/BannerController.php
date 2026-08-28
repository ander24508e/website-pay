<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\LandingBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Muestra la lista de banners (vista principal)
     */
    public function index(Request $request)
    {
        $this->authorize('banners.view');

        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));

        $banners = LandingBanner::query()
            ->where('empresa_id', '=', $empresa->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('etiqueta', 'like', "%{$search}%")
                        ->orWhere('titulo', 'like', "%{$search}%")
                        ->orWhere('texto', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.banners.index', compact('empresa', 'banners'));
    }

    /**
     * Formulario de creacion
     */
    public function create()
    {
        $this->authorize('banners.create');

        $empresa = $this->getOrCreateEmpresa();
        $nextOrder = (int) (LandingBanner::where('empresa_id', $empresa->id)->max('orden') ?? -1) + 1;

        return view('admin.banners.create', compact('empresa', 'nextOrder'));
    }

    /**
     * Vista detalle
     */
    public function show(LandingBanner $banner)
    {
        $this->authorize('banners.view');

        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Formulario de edicion
     */
    public function edit(LandingBanner $banner)
    {
        $this->authorize('banners.update');

        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Almacena un nuevo banner
     */
    public function store(Request $request)
    {
        $this->authorize('banners.create');

        $data = $request->validate([
            'etiqueta' => 'nullable|string|max:80',
            'titulo' => 'nullable|string|max:120',
            'texto' => 'nullable|string|max:400',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'orden' => 'nullable|integer|min:0|max:9999',
            'activo' => 'nullable|boolean',
            'es_principal' => 'nullable|boolean',
        ]);

        $empresa = $this->getOrCreateEmpresa();

        $banner = new LandingBanner;
        $banner->empresa_id = $empresa->id;
        $banner->etiqueta = $this->cleanInput($data['etiqueta'] ?? null);
        $banner->titulo = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto = $this->cleanInput($data['texto'] ?? null);
        $banner->orden = (int) ($data['orden'] ?? 0);
        $banner->activo = (bool) ($data['activo'] ?? true);
        $banner->es_principal = (bool) ($data['es_principal'] ?? false);
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $banner->imagen = $request->file('imagen')->store('landing_banners', 'public');
        }
        $banner->save();

        if ($banner->es_principal) {
            LandingBanner::query()
                ->where('empresa_id', '=', $empresa->id)
                ->where('id', '!=', $banner->id)
                ->where('es_principal', '=', true)
                ->update(['es_principal' => false]);
        }

        $destination = $request->user()->can('banners.view')
            ? 'admin.banners.index'
            : 'admin.banners.create';

        return redirect()->route($destination)->with('success', 'Banner creado correctamente.');
    }

    /**
     * Actualiza un banner existente
     */
    public function update(Request $request, LandingBanner $banner)
    {
        $this->authorize('banners.update');

        $data = $request->validate([
            'etiqueta' => 'nullable|string|max:80',
            'titulo' => 'nullable|string|max:120',
            'texto' => 'nullable|string|max:400',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'orden' => 'nullable|integer|min:0|max:9999',
            'activo' => 'nullable|boolean',
            'es_principal' => 'nullable|boolean',
        ]);

        $empresa = $this->getOrCreateEmpresa();

        $banner->etiqueta = $this->cleanInput($data['etiqueta'] ?? null);
        $banner->titulo = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto = $this->cleanInput($data['texto'] ?? null);
        $banner->orden = (int) ($data['orden'] ?? 0);
        $banner->activo = (bool) ($data['activo'] ?? false);
        $banner->es_principal = (bool) ($data['es_principal'] ?? false);

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            if ($banner->imagen && Storage::disk('public')->exists($banner->imagen)) {
                Storage::disk('public')->delete($banner->imagen);
            }
            $banner->imagen = $request->file('imagen')->store('landing_banners', 'public');
        }

        $banner->save();

        if ($banner->es_principal) {
            LandingBanner::query()
                ->where('empresa_id', '=', $empresa->id)
                ->where('id', '!=', $banner->id)
                ->where('es_principal', '=', true)
                ->update(['es_principal' => false]);
        }

        $destination = $request->user()->can('banners.view')
            ? route('admin.banners.index')
            : route('admin.banners.edit', $banner);

        return redirect($destination)->with('success', 'Banner actualizado correctamente.');
    }

    /**
     * Elimina un banner
     */
    public function destroy(LandingBanner $banner)
    {
        $this->authorize('banners.delete');

        if ($banner->imagen && Storage::disk('public')->exists($banner->imagen)) {
            Storage::disk('public')->delete($banner->imagen);
        }
        LandingBanner::destroy($banner->id);

        $user = request()->user();
        $destination = $user->can('banners.view')
            ? route('admin.banners.index')
            : ($user->can('banners.create') ? route('admin.banners.create') : route('home'));

        return redirect($destination)->with('success', 'Banner eliminado correctamente.');
    }

    // ------------------------------------------------------------
    // Métodos auxiliares privados (copiados del controlador original)
    // ------------------------------------------------------------
    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first(['*']) ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
    }
}
