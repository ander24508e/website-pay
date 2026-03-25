<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.partials.profileEdit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'foto_perfil'=> 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'current_password' => 'nullable|string',
            'password'   => 'nullable|min:8|confirmed',
        ]);

        // 1. Información personal
        $user->name  = $request->name;
        $user->email = $request->email;

        // 2. Foto de perfil
        if ($request->hasFile('foto_perfil') && $request->file('foto_perfil')->isValid()) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $fileName = 'profile-' . $user->id . '-' . time() . '.' . $request->foto_perfil->getClientOriginalExtension();
            $user->foto_perfil = $request->foto_perfil->storeAs('fotos_perfil', $fileName, 'public');
        }

        // 3. Contraseña opcional
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        }

        // 4. Invalidar email si cambió
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