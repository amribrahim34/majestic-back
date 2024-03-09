<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            [
                'publisher_name' => json_encode(['en' => 'Publisher One', 'ar' => 'الناشر واحد']),
                'logo' => 'path/to/logo1.png',
                'location' => 'New York, USA',
                'website' => 'https://publisherone.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'publisher_name' => json_encode(['en' => 'Publisher Two', 'ar' => 'الناشر اثنين']),
                'logo' => 'path/to/logo2.png',
                'location' => 'London, UK',
                'website' => 'https://publishertwo.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Add as many publishers as you want here, don't forget to add created_at and updated_at
        ];

        DB::table('publishers')->insert($publishers);
    }
}
