<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\LandingBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresaQuery = Empresa::query();

        if (Schema::hasTable('landing_banners')) {
            $empresaQuery->with('landingBanners');
        }

        $empresa = $empresaQuery->first() ?? new Empresa();

        return view('profile.empresa.index', compact('empresa'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:255',
            'eslogan' => 'nullable|string|max:255',
            'descripcion_corta' => 'nullable|string|max:1000',
            'descripcion_footer' => 'nullable|string|max:1000',
            'horario' => 'nullable|string|max:255',
            'servicios_resumen' => 'nullable|string|max:255',
            'ubicacion_embed' => 'nullable|string|max:5000',
            'ciudad' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $empresa = Empresa::first() ?? new Empresa();

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            $fileName = 'logo-empresa-' . time() . '.' . $request->logo->getClientOriginalExtension();
            $empresa->logo = $request->logo->storeAs('logos_empresa', $fileName, 'public');
        }

        $empresa->nombre = $this->cleanInput($request->nombre);
        $empresa->direccion = $this->cleanInput($request->direccion);
        $empresa->telefono = $this->cleanInput($request->telefono);
        $empresa->correo = $this->cleanInput($request->correo);
        $empresa->ciudad = $this->cleanInput($request->ciudad);
        $empresa->eslogan = $this->cleanInput($request->eslogan);
        $empresa->descripcion_corta = $this->cleanInput($request->descripcion_corta);
        $empresa->descripcion_footer = $this->cleanInput($request->descripcion_footer);
        $empresa->servicios_resumen = $this->cleanInput($request->servicios_resumen);
        $empresa->horario = $this->cleanInput($request->horario);
        $empresa->ubicacion_embed = $this->normalizeMapInput($request->input('ubicacion_embed'));
        $empresa->save();

        return redirect()->route('admin.empresa.edit')
            ->with('status', 'empresa-updated');
    }

    public function deleteLogo()
    {
        $empresa = Empresa::first();

        if ($empresa && $empresa->logo) {
            if (Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            $empresa->logo = null;
            $empresa->save();

            return back()->with('success', 'Logo eliminado correctamente.');
        }

        return back()->with('error', 'No hay logo para eliminar.');
    }

    public function storeBanner(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:120',
            'texto' => 'nullable|string|max:400',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,webp|max:6144',
            'boton_texto' => 'nullable|string|max:60',
            'boton_link' => 'nullable|string|max:1000',
            'orden' => 'nullable|integer|min:0|max:9999',
            'activo' => 'nullable|boolean',
        ]);

        $empresa = $this->getOrCreateEmpresa();

        $banner = new LandingBanner();
        $banner->empresa_id = $empresa->id;
        $banner->titulo = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto = $this->cleanInput($data['texto'] ?? null);
        $banner->boton_texto = $this->cleanInput($data['boton_texto'] ?? null);
        $banner->boton_link = $this->cleanInput($data['boton_link'] ?? null);
        $banner->orden = (int) ($data['orden'] ?? 0);
        $banner->activo = (bool) ($data['activo'] ?? true);
        $banner->imagen = $request->file('imagen')->store('landing_banners', 'public');
        $banner->save();

        return redirect()->route('admin.empresa.edit')->with('success', 'Banner creado correctamente.');
    }

    public function updateBanner(Request $request, LandingBanner $banner)
    {
        $data = $request->validate([
            'titulo' => 'nullable|string|max:120',
            'texto' => 'nullable|string|max:400',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'boton_texto' => 'nullable|string|max:60',
            'boton_link' => 'nullable|string|max:1000',
            'orden' => 'nullable|integer|min:0|max:9999',
            'activo' => 'nullable|boolean',
        ]);

        $banner->titulo = $this->cleanInput($data['titulo'] ?? null);
        $banner->texto = $this->cleanInput($data['texto'] ?? null);
        $banner->boton_texto = $this->cleanInput($data['boton_texto'] ?? null);
        $banner->boton_link = $this->cleanInput($data['boton_link'] ?? null);
        $banner->orden = (int) ($data['orden'] ?? 0);
        $banner->activo = (bool) ($data['activo'] ?? false);

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            if ($banner->imagen && Storage::disk('public')->exists($banner->imagen)) {
                Storage::disk('public')->delete($banner->imagen);
            }

            $banner->imagen = $request->file('imagen')->store('landing_banners', 'public');
        }

        $banner->save();

        return redirect()->route('admin.empresa.edit')->with('success', 'Banner actualizado correctamente.');
    }

    public function destroyBanner(LandingBanner $banner)
    {
        if ($banner->imagen && Storage::disk('public')->exists($banner->imagen)) {
            Storage::disk('public')->delete($banner->imagen);
        }

        $banner->delete();

        return redirect()->route('admin.empresa.edit')->with('success', 'Banner eliminado correctamente.');
    }

    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeMapInput(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $decoded = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

        if (preg_match('/src=["\']([^"\']+)["\']/i', $decoded, $matches)) {
            return $matches[1];
        }

        return $decoded;
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
    }
}
