<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AttributeSeeder::class);
        // $this->call(EmailTemplateSeeder::class);
        $this->call(LeadPipelineSeeder::class);
        $this->call(LeadTypeSeeder::class);
        $this->call(LeadSourceSeeder::class);
        // $this->call(WorkflowSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleUserTableSeeder::class);
    }
}
