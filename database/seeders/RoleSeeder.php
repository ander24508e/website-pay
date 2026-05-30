<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        // Usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin.endara@gmail.com'],
            [
                'name' => 'Anderson Endara',
                'password' => bcrypt('Admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // Usuario cliente de ejemplo
        $cliente = User::updateOrCreate(
            ['email' => 'cliente.endara@gmail.com'],
            [
                'name' => 'Daniel',
                'password' => bcrypt('Cliente123'),
                'email_verified_at' => now(),
            ]
        );
        $cliente->syncRoles(['cliente']);

        $this->command->info('Roles creados: admin, cliente');
        $this->command->info('Admin: admin.endara@gmail.com / Admin123');
        $this->command->info('Cliente: cliente.endara@gmail.com / Cliente123');
    }
}
