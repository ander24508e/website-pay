<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        $inventoryPermissions = [
            'inventory.view',
            'inventory.move',
            'inventory.void',
            'inventory.view_costs',
            'inventory.export',
            'inventory.close_periods',
        ];

        foreach ($inventoryPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole->givePermissionTo($inventoryPermissions);

        $admin = User::firstOrCreate(
            ['email' => 'admin@endara.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('Admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@test.com'],
            [
                'name' => 'Daniel',
                'password' => bcrypt('Cliente123'),
                'email_verified_at' => now(),
            ]
        );
        $cliente->syncRoles(['cliente']);

        $this->command->info('✅ Roles creados: admin, cliente');
        $this->command->info('✅ Admin: admin@endara.com / Admin123');
        $this->command->info('✅ Cliente: cliente@test.com / Cliente123');
    }
}
