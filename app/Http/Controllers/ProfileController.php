<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $activeTab = $request->query('tab', 'account');
        if (!in_array($activeTab, ['account', 'security'], true)) {
            $activeTab = 'account';
        }

        return view('profile.index', [
            'user' => $request->user(),
            'activeTab' => $activeTab,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        return $this->updateAccount($request);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hasTelefono = Schema::hasColumn('users', 'telefono');
        $hasDireccion = Schema::hasColumn('users', 'direccion');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ];

        if ($hasTelefono) {
            $rules['telefono'] = 'required|string|max:50';
        }

        if ($hasDireccion) {
            $rules['direccion'] = 'nullable|string|max:500';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($hasTelefono) {
            $user->telefono = trim((string) $request->telefono) !== '' ? $request->telefono : null;
        }

        if ($hasDireccion) {
            $user->direccion = trim((string) $request->direccion) !== '' ? $request->direccion : null;
        }

        if ($request->hasFile('foto_perfil') && $request->file('foto_perfil')->isValid()) {
            $foto = $request->file('foto_perfil');

            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $fileName = 'profile-' . $user->id . '-' . time() . '.' . $foto->getClientOriginalExtension();
            $user->foto_perfil = $foto->storeAs('fotos_perfil', $fileName, 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', ['tab' => 'account'])->with('success', 'Datos de la cuenta actualizados correctamente.');
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($data['password']);
        $user->save();

        return Redirect::route('profile.edit', ['tab' => 'security'])->with('success', 'Contrasena actualizada correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
