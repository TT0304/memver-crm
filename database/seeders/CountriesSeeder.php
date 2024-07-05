<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class CountriesSeeder extends Seeder
{
    public function run()
    {
        DB::table('countries')->delete();

        $countries = json_decode(file_get_contents(__DIR__ . '/../Data/countries.json'), true);

        DB::table('countries')->insert($countries);
    }
}