<?php

namespace Database\Seeders;

use App\DataStructure\User\Models\Role;
use App\DataStructure\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        User::findOrFail(1)->roles()->sync(1);
    }
}
