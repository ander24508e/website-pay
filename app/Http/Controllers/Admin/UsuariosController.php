<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $usuarios = User::query()
            ->with('roles')
            ->withCount('orders')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_usuarios' => (int) User::query()->count(),
            'admins' => (int) User::query()->role('admin')->count(),
            'empleados' => (int) User::query()->role('empleado')->count(),
            'clientes' => (int) User::query()->role('cliente')->count(),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'stats', 'search'));
    }

    public function show(User $usuario)
    {
        $usuario->load(['roles', 'orders' => fn ($q) => $q->latest()->take(10)]);

        $resumen = [
            'total_ordenes' => (int) $usuario->orders()->count(),
            'total_comprado' => (float) $usuario->orders()->where('status', 'paid')->sum('total'),
            'registro' => $usuario->created_at,
        ];

        return view('admin.usuarios.show', compact('usuario', 'resumen'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::query()
            ->whereIn('name', ['admin', 'empleado', 'cliente'])
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()
            ->whereIn('name', ['admin', 'empleado', 'cliente'])
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,empleado,cliente'],
        ]);

        $usuario = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
            'direccion' => trim((string) ($data['direccion'] ?? '')) ?: null,
            'password' => $data['password'],
        ]);

        $role = Role::firstOrCreate([
            'name' => $data['role'],
            'guard_name' => 'web',
        ]);

        $usuario->syncRoles([$role->name]);

        return redirect()->route('admin.usuarios.show', $usuario)->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'role' => ['required', 'in:admin,empleado,cliente'],
        ]);

        $usuario->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'telefono' => trim((string) ($data['telefono'] ?? '')) !== '' ? $data['telefono'] : null,
            'direccion' => trim((string) ($data['direccion'] ?? '')) !== '' ? $data['direccion'] : null,
        ]);

        $role = Role::firstOrCreate([
            'name' => $data['role'],
            'guard_name' => 'web',
        ]);

        $usuario->syncRoles([$role->name]);

        return redirect()->route('admin.usuarios.show', $usuario)->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return redirect()->route('admin.usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
