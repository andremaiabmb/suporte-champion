<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Authenticatable;

final class RoleResolver
{
    private static ?array $schema = null;

    private static function bootSchema(): void
    {
        if (self::$schema !== null) return;

        self::$schema = [
            'users_has_role_col' => Schema::hasColumn('users', 'role'),
            'has_roles_table'    => Schema::hasTable('roles'),
            'has_role_user'      => Schema::hasTable('role_user') || Schema::hasTable('user_roles'),
            'role_user_table'    => Schema::hasTable('role_user') ? 'role_user' : (Schema::hasTable('user_roles') ? 'user_roles' : null),
            'has_user_setor'     => Schema::hasTable('user_setor'),
        ];
    }

    /**
     * Retorna todos os papéis do usuário como strings minúsculas.
     */
    public static function rolesOf(Authenticatable $user): array
    {
        self::bootSchema();

        $roles = [];

        // 1) Coluna direta users.role
        if (self::$schema['users_has_role_col']) {
            $val = strtolower((string)($user->role ?? ''));
            if ($val !== '') $roles[] = $val;
        }

        // 2) Tabela roles + pivot role_user/user_roles (campos comuns: role_id, user_id)
        if (self::$schema['has_roles_table'] && self::$schema['has_role_user'] && self::$schema['role_user_table']) {
            $pivot = self::$schema['role_user_table'];

            // Descobrir PKs do pivot
            $pivotCols = DB::getSchemaBuilder()->getColumnListing($pivot);
            $userCol = in_array('user_id', $pivotCols) ? 'user_id' : (in_array('idUser', $pivotCols) ? 'idUser' : null);
            $roleCol = in_array('role_id', $pivotCols) ? 'role_id' : (in_array('idRole', $pivotCols) ? 'idRole' : null);

            if ($userCol && $roleCol) {
                $roleIds = DB::table($pivot)->where($userCol, $user->getAuthIdentifier())->pluck($roleCol)->all();
                if (!empty($roleIds)) {
                    // Descobrir coluna “nome” em roles
                    $roleCols = DB::getSchemaBuilder()->getColumnListing('roles');
                    $nameCol = in_array('name', $roleCols) ? 'name' :
                               (in_array('slug', $roleCols) ? 'slug' :
                               (in_array('titulo', $roleCols) ? 'titulo' : null));

                    if ($nameCol) {
                        $found = DB::table('roles')->whereIn('id', $roleIds)->pluck($nameCol)->all();
                        foreach ($found as $r) $roles[] = strtolower((string)$r);
                    }
                }
            }
        }

        // 3) Estruturas alternativas (ex.: user_setor vira “setor:<id>” para poder usar em gates/menus)
        if (self::$schema['has_user_setor']) {
            $setorCols = DB::getSchemaBuilder()->getColumnListing('user_setor');
            $userCol = in_array('user_id', $setorCols) ? 'user_id' : (in_array('idUser', $setorCols) ? 'idUser' : null);
            $setorCol = in_array('setor_id', $setorCols) ? 'setor_id' : (in_array('idSetor', $setorCols) ? 'idSetor' : null);

            if ($userCol && $setorCol) {
                $setores = DB::table('user_setor')->where($userCol, $user->getAuthIdentifier())->pluck($setorCol)->all();
                foreach ($setores as $sid) $roles[] = 'setor:'.strtolower((string)$sid);
            }
        }

        // Normalizar e remover duplicados
        $roles = array_values(array_unique(array_filter($roles)));
        return $roles;
    }

    public static function hasAny(Authenticatable $user, array $allowed): bool
    {
        $allowed = array_map(fn($r) => strtolower((string)$r), $allowed);
        $userRoles = self::rolesOf($user);

        // Match direto
        foreach ($userRoles as $r) {
            if (in_array($r, $allowed, true)) return true;
        }

        // Suporte a curinga tipo "setor:*" (se você quiser usar no futuro)
        if (in_array('setor:*', $allowed, true) && array_filter($userRoles, fn($r) => str_starts_with($r, 'setor:'))) {
            return true;
        }

        return false;
    }
}
