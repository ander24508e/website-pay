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
        $empresa = $this->getOrCreateEmpresa();
        $search = trim((string) $request->query('q', ''));

        $banners = LandingBanner::query()
            ->where('empresa_id', '=', $empresa->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('titulo', 'like', "%{$search}%")
                        ->orWhere('texto', 'like', "%{$search}%")
                        ->orWhere('boton_texto', 'like', "%{$search}%")
                        ->orWhere('boton_link', 'like', "%{$search}%");
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
        $empresa = $this->getOrCreateEmpresa();

        return view('admin.banners.create', compact('empresa'));
    }

    /**
     * Vista detalle
     */
    public function show(LandingBanner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Formulario de edicion
     */
    public function edit(LandingBanner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Almacena un nuevo banner
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'nullable|string|max:120',
            'texto'       => 'nullable|string|max:400',
            'imagen'      => 'required|image|mimes:jpeg,png,jpg,webp|max:6144',
            'boton_texto' => 'nullable|string|max:60',
            'boton_link'  => 'nullable|string|max:1000',
            'activo'      => 'nullable|boolean',
            'es_principal' => 'nullable|boolean',
        ]);

        $empresa = $this->getOrCreateEmpresa();

        $banner = new LandingBanner();
        $banner->empresa_id = $empresa->id;
        $banner->titulo      = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto       = $this->cleanInput($data['texto'] ?? null);
        $banner->boton_texto = $this->cleanInput($data['boton_texto'] ?? null);
        $banner->boton_link  = $this->cleanInput($data['boton_link'] ?? null);
        $banner->activo      = (bool) ($data['activo'] ?? true);
        $banner->es_principal = (bool) ($data['es_principal'] ?? false);
        $banner->imagen      = $request->file('imagen')->store('landing_banners', 'public');
        $banner->save();

        if ($banner->es_principal) {
            LandingBanner::query()
                ->where('empresa_id', '=', $empresa->id)
                ->where('id', '!=', $banner->id)
                ->where('es_principal', '=', true)
                ->update(['es_principal' => false]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado correctamente.');
    }

    /**
     * Actualiza un banner existente
     */
    public function update(Request $request, LandingBanner $banner)
    {
        $data = $request->validate([
            'titulo'      => 'nullable|string|max:120',
            'texto'       => 'nullable|string|max:400',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'boton_texto' => 'nullable|string|max:60',
            'boton_link'  => 'nullable|string|max:1000',
            'activo'      => 'nullable|boolean',
            'es_principal' => 'nullable|boolean',
        ]);

        $empresa = $this->getOrCreateEmpresa();

        $banner->titulo      = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto       = $this->cleanInput($data['texto'] ?? null);
        $banner->boton_texto = $this->cleanInput($data['boton_texto'] ?? null);
        $banner->boton_link  = $this->cleanInput($data['boton_link'] ?? null);
        $banner->activo      = (bool) ($data['activo'] ?? false);
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

        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizado correctamente.');
    }

    /**
     * Elimina un banner
     */
    public function destroy(LandingBanner $banner)
    {
        if ($banner->imagen && Storage::disk('public')->exists($banner->imagen)) {
            Storage::disk('public')->delete($banner->imagen);
        }
        LandingBanner::destroy($banner->id);

        return redirect()->route('admin.banners.index')->with('success', 'Banner eliminado correctamente.');
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
