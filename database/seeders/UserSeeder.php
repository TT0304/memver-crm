<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\DataStructure\User\Models\Permission;
use App\DataStructure\User\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'id'              => 1,
            'name'            => 'Mealas',
        ]);

         // Create Admin role
         User::create([
            'id'              => 1,
            'name'            => 'kevin',
            'email'           => 'tarun@themesbrand.com',
            'password'        => bcrypt('12345678'),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'status'          => 1,
            'view_permission' => 'global',
            'client_id'       => 1,
            
        ]);

        // $admin_permissions = Permission::all();
        // Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));
    }
}
