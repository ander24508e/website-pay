<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Empresa::first() ?? new Empresa();
        return view('profile.empresa.index', compact('empresa'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono'  => 'nullable|string|max:20',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        $empresa = Empresa::first() ?? new Empresa();

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $fileName = 'logo-empresa-' . time() . '.' . $request->logo->getClientOriginalExtension();
            $empresa->logo = $request->logo->storeAs('logos_empresa', $fileName, 'public');
        }

        $empresa->nombre    = $request->nombre;
        $empresa->direccion = $request->direccion ?? '';
        $empresa->telefono  = $request->telefono ?? '';
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
}