<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private const ROLES = [
        'admin',
        'empleado',
        'cliente',
    ];

    public function run(): void
    {
        $permissionRegistrar = app(\Spatie\Permission\PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        foreach (self::ROLES as $role) {
            Role::updateOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                ['name' => $role, 'guard_name' => 'web']
            );
        }

        $permissionRegistrar->forgetCachedPermissions();

        $this->command->info('Roles created: '.implode(', ', self::ROLES));
    }
}
