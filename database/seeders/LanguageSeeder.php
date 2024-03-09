<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['language_name' => 'English', 'iso_code' => 'EN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['language_name' => 'العربية', 'iso_code' => 'AR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('languages')->insert($languages);
    }
}
