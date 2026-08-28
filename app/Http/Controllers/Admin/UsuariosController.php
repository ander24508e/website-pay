<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    private const STAFF_ROLES = ['gerente', 'empleado'];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $actor = $request->user();

        $usuarios = $this->visibleStaffQuery($actor)
            ->with(['roles', 'manager:id,name'])
            ->withCount('assignedOrders')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn (Builder $query) => $query->where('active', true))
            ->when($status === 'inactive', fn (Builder $query) => $query->where('active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statsQuery = $this->visibleStaffQuery($actor);
        $stats = [
            'total_usuarios' => (clone $statsQuery)->count(),
            'gerentes' => (clone $statsQuery)->role('gerente')->count(),
            'empleados' => (clone $statsQuery)->role('empleado')->count(),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'stats', 'search', 'status'));
    }

    public function show(Request $request, User $usuario)
    {
        $this->ensureVisible($request->user(), $usuario);

        $usuario->load([
            'roles', 'permissions', 'manager:id,name', 'createdBy:id,name',
            'assignedOrders' => fn ($query) => $query->latest()->take(10),
            'audits.actor:id,name',
        ]);

        $resumen = [
            'ordenes_asignadas' => $usuario->assignedOrders()->count(),
            'ventas_atendidas' => $usuario->attendedSales()->count(),
            'total_vendido' => (float) $usuario->attendedSales()->where('status', 'paid')->sum('total'),
            'registro' => $usuario->created_at,
        ];

        return view('admin.usuarios.show', compact('usuario', 'resumen'));
    }

    public function create(Request $request)
    {
        $actor = $request->user();
        $roles = $this->assignableRoles($actor);
        abort_if($roles->isEmpty(), 403);

        return view('admin.usuarios.create', $this->formData($actor, $roles));
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        $roles = $this->assignableRoles($actor)->pluck('name')->all();
        abort_if(empty($roles), 403);

        $permissionNames = $this->assignablePermissions($actor)->pluck('name')->all();
        $data = $request->validate($this->rules($roles, $permissionNames));

        $usuario = DB::transaction(function () use ($actor, $data) {
            $managerId = $actor->hasRole('gerente')
                ? $actor->id
                : $this->validatedManagerId($data['manager_id'] ?? null);

            $usuario = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'telefono' => $this->clean($data['telefono'] ?? null),
                'direccion' => $this->clean($data['direccion'] ?? null),
                'password' => $data['password'],
                'active' => (bool) ($data['active'] ?? true),
                'created_by' => $actor->id,
                'manager_id' => $data['role'] === 'empleado' ? $managerId : null,
            ]);

            $usuario->syncRoles([$data['role']]);
            if ($this->canAssignPermissions($actor)) {
                $usuario->syncPermissions($data['permissions'] ?? []);
            }

            $this->audit($usuario, $actor, 'staff.created', [
                'role' => $data['role'],
                'permissions' => $data['permissions'] ?? [],
            ]);

            return $usuario;
        });

        return redirect()->route('admin.usuarios.show', $usuario)
            ->with('success', 'Personal creado correctamente.');
    }

    public function edit(Request $request, User $usuario)
    {
        $actor = $request->user();
        $this->ensureManageable($actor, $usuario);

        return view('admin.usuarios.edit', [
            'usuario' => $usuario->load(['roles', 'permissions']),
            ...$this->formData($actor, $this->assignableRoles($actor)),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $actor = $request->user();
        $this->ensureManageable($actor, $usuario);

        $roles = $this->assignableRoles($actor)->pluck('name')->all();
        $permissionNames = $this->assignablePermissions($actor)->pluck('name')->all();
        $data = $request->validate($this->rules($roles, $permissionNames, $usuario));

        DB::transaction(function () use ($actor, $usuario, $data) {
            $before = [
                'role' => $usuario->roles->pluck('name')->first(),
                'active' => $usuario->active,
                'permissions' => $usuario->getDirectPermissions()->pluck('name')->all(),
            ];

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'telefono' => $this->clean($data['telefono'] ?? null),
                'direccion' => $this->clean($data['direccion'] ?? null),
                'manager_id' => $data['role'] === 'empleado'
                    ? ($actor->hasRole('gerente') ? $actor->id : $this->validatedManagerId($data['manager_id'] ?? null))
                    : null,
            ];

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }
            if ($actor->isOwner() || $actor->can('users.deactivate')) {
                $payload['active'] = (bool) ($data['active'] ?? false);
            }

            $usuario->update($payload);
            $usuario->syncRoles([$data['role']]);
            if ($this->canAssignPermissions($actor)) {
                $usuario->syncPermissions($data['permissions'] ?? []);
            }

            $this->audit($usuario, $actor, 'staff.updated', [
                'before' => $before,
                'after' => [
                    'role' => $data['role'],
                    'active' => $usuario->active,
                    'permissions' => $data['permissions'] ?? $before['permissions'],
                ],
            ]);
        });

        return redirect()->route('admin.usuarios.show', $usuario)
            ->with('success', 'Personal actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        $actor = $request->user();
        $this->ensureManageable($actor, $usuario);
        abort_unless($actor->isOwner() || $actor->can('users.deactivate'), 403);

        $usuario->update(['active' => false]);
        $this->audit($usuario, $actor, 'staff.deactivated');

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'El acceso del trabajador fue desactivado; su historial se conserva.');
    }

    private function rules(array $roles, array $permissions, ?User $usuario = null): array
    {
        $uniqueEmail = Rule::unique('users', 'email');
        if ($usuario) {
            $uniqueEmail->ignore($usuario->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $uniqueEmail],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'password' => [$usuario ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in($roles)],
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in($permissions)],
        ];
    }

    private function visibleStaffQuery(User $actor): Builder
    {
        return User::query()
            ->where('is_owner', false)
            ->whereHas('roles', fn (Builder $query) => $query->whereIn('name', self::STAFF_ROLES))
            ->when(! $actor->isOwner(), function (Builder $query) use ($actor) {
                $query->whereHas('roles', fn (Builder $roles) => $roles->where('name', 'empleado'))
                    ->where(function (Builder $staff) use ($actor) {
                        $staff->where('manager_id', $actor->id)->orWhere('created_by', $actor->id);
                    });
            });
    }

    private function ensureVisible(User $actor, User $target): void
    {
        abort_unless($this->visibleStaffQuery($actor)->whereKey($target->id)->exists(), 404);
    }

    private function ensureManageable(User $actor, User $target): void
    {
        $this->ensureVisible($actor, $target);
        abort_if($target->isOwner() || $target->is($actor), 403);
        abort_if(! $actor->isOwner() && ! $target->hasRole('empleado'), 403);
    }

    private function assignableRoles(User $actor)
    {
        $names = $actor->isOwner()
            ? ['gerente', 'empleado']
            : ($actor->can('users.create_employees') ? ['empleado'] : []);

        return Role::query()->whereIn('name', $names)->orderBy('name')->get();
    }

    private function assignablePermissions(User $actor)
    {
        $query = Permission::query()->where('guard_name', 'web')->orderBy('name');

        if (! $actor->isOwner()) {
            $query->whereIn('name', $actor->getAllPermissions()->pluck('name'))
                ->where('name', 'not like', 'users.%');
        }

        return $query->get();
    }

    private function permissionGroups(User $actor): array
    {
        $labels = [
            'dashboard' => 'Dashboard', 'users' => 'Usuarios', 'sales' => 'Ventas',
            'transactions' => 'Pagos', 'orders' => 'Ordenes', 'clients' => 'Clientes',
            'vehicles' => 'Vehiculos', 'catalog' => 'Catalogo', 'inventory' => 'Inventario',
            'company' => 'Empresa', 'banners' => 'Banners',
        ];
        $operationLabels = [
            'banners.view' => 'Ver banners',
            'banners.create' => 'Crear banners',
            'banners.update' => 'Editar banners',
            'banners.delete' => 'Eliminar banners',
        ];

        return $this->assignablePermissions($actor)
            ->groupBy(fn (Permission $permission) => str($permission->name)->before('.')->toString())
            ->map(function ($items, string $group) use ($labels, $operationLabels): array {
                $items->each(function (Permission $permission) use ($operationLabels): void {
                    $permission->setAttribute(
                        'display_name',
                        $operationLabels[$permission->name]
                            ?? str($permission->name)->after('.')->replace('_', ' ')->headline()
                    );
                });

                return [
                    'label' => $labels[$group] ?? ucfirst($group),
                    'permissions' => $items,
                ];
            })->all();
    }

    private function formData(User $actor, $roles): array
    {
        return [
            'roles' => $roles,
            'permissionGroups' => $this->permissionGroups($actor),
            'managers' => $this->availableManagers($actor),
            'canAssignPermissions' => $this->canAssignPermissions($actor),
        ];
    }

    private function availableManagers(User $actor)
    {
        return $actor->isOwner()
            ? User::query()->role('gerente')->where('active', true)->orderBy('name')->get(['id', 'name'])
            : collect([$actor]);
    }

    private function canAssignPermissions(User $actor): bool
    {
        return $actor->isOwner() || $actor->can('users.manage_permissions');
    }

    private function validatedManagerId(mixed $managerId): ?int
    {
        if (! $managerId) {
            return null;
        }

        return User::query()->role('gerente')->where('active', true)->findOrFail((int) $managerId)->id;
    }

    private function audit(User $target, User $actor, string $action, array $payload = []): void
    {
        UserAudit::create([
            'target_user_id' => $target->id,
            'actor_user_id' => $actor->id,
            'action' => $action,
            'payload' => $payload ?: null,
        ]);
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
