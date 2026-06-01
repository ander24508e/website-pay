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
        $user = $request->user();
        $hasTelefono = Schema::hasColumn('users', 'telefono');
        $hasDireccion = Schema::hasColumn('users', 'direccion');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'current_password' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ];

        if ($hasTelefono) {
            $rules['telefono'] = 'required|string|max:50';
        }

        if ($hasDireccion) {
            $rules['direccion'] = 'nullable|string|max:500';
        }

        $request->validate($rules);

        // 1. Informacion personal
        $user->name = $request->name;
        $user->email = $request->email;

        if ($hasTelefono) {
            $user->telefono = trim((string) $request->telefono) !== '' ? $request->telefono : null;
        }

        if ($hasDireccion) {
            $user->direccion = trim((string) $request->direccion) !== '' ? $request->direccion : null;
        }

        // 2. Foto de perfil
        if ($request->hasFile('foto_perfil') && $request->file('foto_perfil')->isValid()) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $fileName = 'profile-' . $user->id . '-' . time() . '.' . $request->foto_perfil->getClientOriginalExtension();
            $user->foto_perfil = $request->foto_perfil->storeAs('fotos_perfil', $fileName, 'public');
        }

        // 3. Contrasena opcional
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contrasena actual no es correcta.']);
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        }

        // 4. Invalidar email si cambio
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
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
