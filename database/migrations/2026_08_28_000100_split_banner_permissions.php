<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const GUARD = 'web';

    private const LEGACY_PERMISSION = 'banners.manage';

    private const GRANULAR_PERMISSIONS = [
        'banners.create',
        'banners.update',
        'banners.delete',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function (): void {
            $permissions = collect(self::GRANULAR_PERMISSIONS)
                ->mapWithKeys(fn (string $name) => [
                    $name => Permission::firstOrCreate([
                        'name' => $name,
                        'guard_name' => self::GUARD,
                    ]),
                ]);

            $legacy = Permission::query()
                ->where('name', self::LEGACY_PERMISSION)
                ->where('guard_name', self::GUARD)
                ->first();

            if (! $legacy) {
                return;
            }

            foreach ($permissions as $permission) {
                $this->copyPermissionAssignments($legacy->id, $permission->id);
            }

            $legacy->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function (): void {
            $legacy = Permission::firstOrCreate([
                'name' => self::LEGACY_PERMISSION,
                'guard_name' => self::GUARD,
            ]);

            $granularIds = Permission::query()
                ->where('guard_name', self::GUARD)
                ->whereIn('name', self::GRANULAR_PERMISSIONS)
                ->pluck('id');

            if ($granularIds->count() === count(self::GRANULAR_PERMISSIONS)) {
                $this->restoreCompleteAssignments($granularIds->all(), $legacy->id);
            }

            Permission::query()
                ->where('guard_name', self::GUARD)
                ->whereIn('name', self::GRANULAR_PERMISSIONS)
                ->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function copyPermissionAssignments(int $sourceId, int $targetId): void
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $permissionKey = $columns['permission_pivot_key'] ?? 'permission_id';

        foreach ([$tables['role_has_permissions'], $tables['model_has_permissions']] as $table) {
            $rows = DB::table($table)
                ->where($permissionKey, $sourceId)
                ->get()
                ->map(function (object $row) use ($permissionKey, $targetId): array {
                    $assignment = (array) $row;
                    $assignment[$permissionKey] = $targetId;

                    return $assignment;
                })
                ->all();

            if ($rows !== []) {
                DB::table($table)->insertOrIgnore($rows);
            }
        }
    }

    private function restoreCompleteAssignments(array $sourceIds, int $targetId): void
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $permissionKey = $columns['permission_pivot_key'] ?? 'permission_id';
        $roleKey = $columns['role_pivot_key'] ?? 'role_id';
        $modelKey = $columns['model_morph_key'] ?? 'model_id';

        $roleRows = DB::table($tables['role_has_permissions'])
            ->whereIn($permissionKey, $sourceIds)
            ->groupBy($roleKey)
            ->havingRaw('COUNT(DISTINCT '.$permissionKey.') = ?', [count($sourceIds)])
            ->pluck($roleKey)
            ->map(fn ($roleId) => [$permissionKey => $targetId, $roleKey => $roleId])
            ->all();

        if ($roleRows !== []) {
            DB::table($tables['role_has_permissions'])->insertOrIgnore($roleRows);
        }

        $modelColumns = [$modelKey, 'model_type'];
        if (config('permission.teams')) {
            $modelColumns[] = $columns['team_foreign_key'];
        }

        $modelRows = DB::table($tables['model_has_permissions'])
            ->select($modelColumns)
            ->whereIn($permissionKey, $sourceIds)
            ->groupBy($modelColumns)
            ->havingRaw('COUNT(DISTINCT '.$permissionKey.') = ?', [count($sourceIds)])
            ->get()
            ->map(function (object $row) use ($permissionKey, $targetId): array {
                return [$permissionKey => $targetId, ...(array) $row];
            })
            ->all();

        if ($modelRows !== []) {
            DB::table($tables['model_has_permissions'])->insertOrIgnore($modelRows);
        }
    }
};
