<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_clients_cannot_access_staff_management(): void
    {
        $client = User::factory()->create();
        $client->assignRole('cliente');

        $this->actingAs($client)
            ->get(route('admin.usuarios.index'))
            ->assertForbidden();
    }

    public function test_system_owner_has_every_permission_and_is_not_managed_as_staff(): void
    {
        $owner = User::query()->where('is_owner', true)->firstOrFail();

        $this->assertTrue($owner->isOwner());
        $this->assertTrue($owner->hasExactRoles(['admin']));
        $this->assertEqualsCanonicalizing(
            Permission::query()->pluck('name')->all(),
            $owner->getAllPermissions()->pluck('name')->all(),
        );

        $this->actingAs($owner)
            ->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertDontSee($owner->email);

        $this->actingAs($owner)
            ->get(route('admin.usuarios.show', $owner))
            ->assertNotFound();
    }

    public function test_owner_identity_cannot_be_assigned_through_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Cuenta ordinaria',
            'email' => 'ordinary@example.com',
            'password' => 'password',
            'is_owner' => true,
        ]);

        $this->assertFalse($user->isOwner());
    }

    public function test_authorized_manager_can_create_only_employees(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('gerente');
        $manager->givePermissionTo(['users.create_employees', 'users.view']);

        $response = $this->actingAs($manager)->post(route('admin.usuarios.store'), [
            'name' => 'Empleado Nuevo',
            'email' => 'empleado@example.com',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'role' => 'empleado',
            'active' => 1,
        ]);

        $employee = User::where('email', 'empleado@example.com')->firstOrFail();
        $response->assertRedirect(route('admin.usuarios.show', $employee));
        $this->assertTrue($employee->hasExactRoles(['empleado']));
        $this->assertSame($manager->id, $employee->manager_id);
        $this->assertSame($manager->id, $employee->created_by);

        $this->actingAs($manager)->post(route('admin.usuarios.store'), [
            'name' => 'Gerente Ilegal',
            'email' => 'gerente-ilegal@example.com',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'role' => 'gerente',
            'active' => 1,
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'gerente-ilegal@example.com']);
    }

    public function test_inactive_staff_cannot_login(): void
    {
        $employee = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
            'active' => false,
        ]);
        $employee->assignRole('empleado');

        $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }
}
