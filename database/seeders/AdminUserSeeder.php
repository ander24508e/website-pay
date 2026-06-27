<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Administrador');

        if (! $email || ! $password) {
            $this->command->warn('Admin user skipped. Set ADMIN_EMAIL and ADMIN_PASSWORD to create one.');

            return;
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['admin']);

        $this->command->info("Admin user ready: {$email}");
    }
}
