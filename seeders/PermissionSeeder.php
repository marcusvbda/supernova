<?php

namespace marcusvbda\supernova\seeders;

use App\Models\Permission;
use App\Models\PermissionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    const CREATE = ['create', 'cadastrar'];
    const EDIT = ['edit', 'editar'];
    const DELETE = ['delete', 'excluir'];
    const COMPLETE_PERMISSIONS = [self::CREATE, self::EDIT, self::DELETE];

    public function makePermissions($type, $value, $permissions = self::COMPLETE_PERMISSIONS)
    {
        $createdPermissions = [];
        DB::beginTransaction();
        $type = PermissionType::updateOrCreate(
            ['name' => $type],
            ['name' => $type]
        );
        foreach ($permissions as $permission) {
            $valueIndex = $permission[0];
            $valueTranslate = $permission[1];
            $permissionName = ucfirst(strtolower("$valueTranslate $type->name"));
            $permissionKey = "$valueIndex-$value";

            $createdPermissions[] = Permission::updateOrCreate(
                ['type_id' => $type->id, 'key' => $permissionKey],
                ['name' => $permissionName, 'type_id' => $type->id, 'key' => $permissionKey]
            );
        }
        DB::commit();
        return $createdPermissions;
    }


    public function deletePermissionType($type)
    {
        $createdPermissions = [];
        DB::beginTransaction();
        PermissionType::where('name', $type)->delete();
        $ids = PermissionType::where('name', $type)->pluck('id')->toArray();
        DB::table('access_group_permissions')->whereIn('permission_id', $ids)->delete();
        Permission::whereIn('id', $ids)->delete();
        DB::commit();
        return $createdPermissions;
    }
}
