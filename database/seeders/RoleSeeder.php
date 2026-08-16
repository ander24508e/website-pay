<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'gerente', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create_employees',
            'users.create_managers',
            'users.update',
            'users.deactivate',
            'users.manage_permissions',
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',
            'sales.collect',
            'transactions.view',
            'orders.view',
            'orders.update',
            'orders.delete',
            'clients.view',
            'clients.manage',
            'vehicles.view',
            'vehicles.manage',
            'catalog.view',
            'catalog.manage',
            'inventory.view',
            'inventory.move',
            'inventory.void',
            'inventory.view_costs',
            'inventory.export',
            'inventory.close_periods',
            'company.view',
            'company.manage',
            'banners.view',
            'banners.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole->syncPermissions($permissions);
        $managerRole->syncPermissions([]);
        $employeeRole->syncPermissions([]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@endara.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('Admin123'),
                'email_verified_at' => now(),
                'active' => true,
            ]
        );
        $admin->update(['active' => true]);
        $admin->syncRoles(['admin']);

        $client = User::firstOrCreate(
            ['email' => 'cliente@test.com'],
            [
                'name' => 'Daniel',
                'password' => bcrypt('Cliente123'),
                'email_verified_at' => now(),
                'active' => true,
            ]
        );
        $client->syncRoles(['cliente']);

        $this->command?->info('Roles y permisos de personal sincronizados.');
    }
}
