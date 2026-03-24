<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché primero
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles — firstOrCreate evita duplicados
        Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cliente',  'guard_name' => 'web']);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@endara.com'],
            [
                'name'              => 'Anderson Endara',
                'password'          => bcrypt('Admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $this->command->info('✅ Roles creados: admin, cliente');
        $this->command->info('✅ Admin: admin@endara.com / Admin123');
    }
}