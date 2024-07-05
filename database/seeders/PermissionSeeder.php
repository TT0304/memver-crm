<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\DataStructure\User\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'guard_name'    => 'web',
                'name' => 'settings_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'permission_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'permission_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'permission_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'permission_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'permission_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'role_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'role_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'role_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'role_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'role_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'user_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'user_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'user_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'user_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'user_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_compose_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_inbox_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_draft_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_outbox_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_sent_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mail_trash_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mailTemplate_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mailTemplate_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mailTemplate_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mailTemplate_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'mailTemplate_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'lead_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'lead_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'lead_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'lead_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'lead_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'leadTag_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'organization_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'organization_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'organization_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'organization_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'organization_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'person_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'person_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'person_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'person_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'person_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'product_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'product_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'product_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'product_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'product_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_create',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_show',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_delete',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'quote_print',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'customize_access',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'profile_password_edit',
            ],
            [
                'guard_name'    => 'web',
                'name' => 'contacts_access',
            ],
            
        ];
        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission['name'])->where('guard_name', $permission['guard_name'])->exists()) {
                Permission::create($permission);
            }
        }
    }
}
