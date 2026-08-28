<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\LandingBanner;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BannerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->empresa = Empresa::create(['nombre' => 'Lavadora Endara']);
    }

    public function test_owner_has_absolute_access_without_permission_assignments(): void
    {
        $owner = User::query()->where('is_owner', true)->firstOrFail();
        Role::findByName('admin')->syncPermissions([]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $owner->refresh();

        $banner = $this->banner();

        $this->actingAs($owner)->get(route('admin.banners.index'))->assertOk();
        $this->actingAs($owner)->get(route('admin.banners.create'))->assertOk();
        $this->actingAs($owner)->get(route('admin.banners.show', $banner))->assertOk();
        $this->actingAs($owner)->get(route('admin.banners.edit', $banner))->assertOk();
        $this->actingAs($owner)->put(route('admin.banners.update', $banner), [
            'titulo' => 'Actualizado por owner',
        ])->assertRedirect(route('admin.banners.index'));
        $this->actingAs($owner)->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseMissing('landing_banners', ['id' => $banner->id]);
    }

    public function test_read_only_user_can_list_and_view_but_cannot_mutate_banners(): void
    {
        $user = $this->staffWith('banners.view');
        $banner = $this->banner();

        $this->actingAs($user)->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Ver')
            ->assertDontSee('Nuevo Banner')
            ->assertDontSee('Editar')
            ->assertDontSee('Eliminar');
        $this->actingAs($user)->get(route('admin.banners.show', $banner))->assertOk();
        $this->actingAs($user)->get(route('admin.banners.create'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.banners.store'), [])->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.edit', $banner))->assertForbidden();
        $this->actingAs($user)->put(route('admin.banners.update', $banner), [])->assertForbidden();
        $this->actingAs($user)->delete(route('admin.banners.destroy', $banner))->assertForbidden();
    }

    public function test_create_only_user_enters_the_form_from_menu_and_can_create(): void
    {
        $user = $this->staffWith('banners.create');

        $this->actingAs($user)->get(route('admin.banners.create'))
            ->assertOk()
            ->assertSee('href="'.route('admin.banners.create').'"', false);
        $this->actingAs($user)->get(route('admin.banners.index'))->assertForbidden();

        $this->actingAs($user)->post(route('admin.banners.store'), [
            'titulo' => 'Banner creado',
            'orden' => 2,
            'activo' => 1,
        ])->assertRedirect(route('admin.banners.create'));

        $this->assertDatabaseHas('landing_banners', [
            'empresa_id' => $this->empresa->id,
            'titulo' => 'Banner creado',
        ]);
    }

    public function test_update_only_user_can_edit_and_update_but_not_view_or_delete(): void
    {
        $user = $this->staffWith('banners.update');
        $banner = $this->banner();

        $this->actingAs($user)->get(route('admin.banners.edit', $banner))
            ->assertOk()
            ->assertDontSee('Eliminar Banner');
        $this->actingAs($user)->put(route('admin.banners.update', $banner), [
            'titulo' => 'Banner actualizado',
            'orden' => 5,
        ])->assertRedirect(route('admin.banners.edit', $banner));
        $this->actingAs($user)->get(route('admin.banners.show', $banner))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.banners.destroy', $banner))->assertForbidden();

        $this->assertDatabaseHas('landing_banners', [
            'id' => $banner->id,
            'titulo' => 'Banner actualizado',
            'orden' => 5,
        ]);
    }

    public function test_delete_only_user_can_delete_but_cannot_read_or_edit(): void
    {
        $user = $this->staffWith('banners.delete');
        $banner = $this->banner();

        $this->actingAs($user)->get(route('admin.banners.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.show', $banner))->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.edit', $banner))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('home'));

        $this->assertDatabaseMissing('landing_banners', ['id' => $banner->id]);
    }

    public function test_user_without_banner_permissions_gets_forbidden_and_module_is_hidden(): void
    {
        $user = $this->staffWith();
        $banner = $this->banner();

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSee(route('admin.banners.index'), false)
            ->assertDontSee(route('admin.banners.create'), false);
        $this->actingAs($user)->get(route('admin.banners.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.create'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.show', $banner))->assertForbidden();
        $this->actingAs($user)->get(route('admin.banners.edit', $banner))->assertForbidden();
        $this->actingAs($user)->post(route('admin.banners.store'), [])->assertForbidden();
        $this->actingAs($user)->put(route('admin.banners.update', $banner), [])->assertForbidden();
        $this->actingAs($user)->delete(route('admin.banners.destroy', $banner))->assertForbidden();
    }

    public function test_user_permission_form_lists_the_four_banner_operations(): void
    {
        $owner = User::query()->where('is_owner', true)->firstOrFail();

        $this->actingAs($owner)->get(route('admin.usuarios.create'))
            ->assertOk()
            ->assertSee('Ver banners')
            ->assertSee('Crear banners')
            ->assertSee('Editar banners')
            ->assertSee('Eliminar banners')
            ->assertSee('value="banners.view"', false)
            ->assertSee('value="banners.create"', false)
            ->assertSee('value="banners.update"', false)
            ->assertSee('value="banners.delete"', false)
            ->assertDontSee('banners.manage');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $banner = $this->banner();

        $this->get(route('admin.banners.index'))->assertRedirect(route('login'));
        $this->get(route('admin.banners.create'))->assertRedirect(route('login'));
        $this->get(route('admin.banners.show', $banner))->assertRedirect(route('login'));
    }

    public function test_invalid_or_missing_banner_routes_return_not_found(): void
    {
        $viewer = $this->staffWith('banners.view');
        $updater = $this->staffWith('banners.update');

        $this->actingAs($viewer)->get('/admin/banners/not-a-number')->assertNotFound();
        $this->actingAs($viewer)->get(route('admin.banners.show', 999999))->assertNotFound();
        $this->actingAs($updater)->get(route('admin.banners.edit', 999999))->assertNotFound();
    }

    public function test_create_is_always_resolved_as_the_static_route(): void
    {
        $creator = $this->staffWith('banners.create');

        $this->actingAs($creator)->get('/admin/banners/create')
            ->assertOk()
            ->assertViewIs('admin.banners.create');
    }

    public function test_legacy_manage_assignments_are_migrated_to_all_mutation_permissions(): void
    {
        $legacy = Permission::create(['name' => 'banners.manage', 'guard_name' => 'web']);
        $directUser = $this->staffWith();
        $directUser->givePermissionTo($legacy);
        $role = Role::findByName('gerente');
        $role->givePermissionTo($legacy);

        $migration = require database_path('migrations/2026_08_28_000100_split_banner_permissions.php');
        $migration->up();

        $this->assertDatabaseMissing('permissions', ['name' => 'banners.manage']);
        $this->assertTrue($directUser->fresh()->hasAllPermissions([
            'banners.create',
            'banners.update',
            'banners.delete',
        ]));
        $this->assertTrue($role->fresh()->hasAllPermissions([
            'banners.create',
            'banners.update',
            'banners.delete',
        ]));
    }

    private function staffWith(string ...$permissions): User
    {
        $user = User::factory()->create();
        $user->assignRole('empleado');

        if ($permissions !== []) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    private function banner(): LandingBanner
    {
        return LandingBanner::create([
            'empresa_id' => $this->empresa->id,
            'titulo' => 'Banner de prueba',
            'orden' => 1,
            'activo' => true,
        ]);
    }
}
