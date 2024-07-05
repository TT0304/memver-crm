<?php

namespace Database\Seeders;

use App\DataStructure\User\Models\Permission;
use App\DataStructure\User\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();
        // Check if Role with ID 1 exists before attempting to sync
        $role = Role::find(1);
        
        if ($role) {  // Only if the role exists
            $role->permissions()->sync($admin_permissions->pluck('id'));
        } else {
            echo "Role with ID 1 not found. Cannot sync permissions.";
        }
    }
}
