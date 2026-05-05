<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::query()->first() ?? new Empresa();
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
            'color_primario' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secundario' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_terciario' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $empresa = Empresa::query()->first() ?? new Empresa();

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logo = $request->file('logo');
            $realPath = $logo->getRealPath();

            if (!$realPath) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'logo' => 'No se pudo procesar el archivo subido. Intenta seleccionar la imagen nuevamente.',
                    ]);
            }

            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            $extension = $logo->extension() ?: 'png';
            $fileName = 'logo-empresa-' . time() . '.' . $extension;
            $empresa->logo = $logo->storeAs('logos_empresa', $fileName, 'public');
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
        $empresa->color_primario = $this->normalizeColorInput($request->input('color_primario'));
        $empresa->color_secundario = $this->normalizeColorInput($request->input('color_secundario'));
        $empresa->color_terciario = $this->normalizeColorInput($request->input('color_terciario'));
        $empresa->save();

        return redirect()->route('admin.empresa.edit')
            ->with('status', 'empresa-updated');
    }

    public function deleteLogo()
    {
        $empresa = Empresa::query()->first();

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

    // ------------------------------------------------------------
    // Métodos auxiliares privados (solo para empresa)
    // ------------------------------------------------------------
    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeMapInput(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $decoded = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        if (preg_match('/src=["\']([^"\']+)["\']/i', $decoded, $matches)) {
            return $matches[1];
        }
        return $decoded;
    }

    private function normalizeColorInput(?string $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        if ($value === '') return null;
        return preg_match('/^#[0-9A-F]{6}$/', $value) ? $value : null;
    }
}