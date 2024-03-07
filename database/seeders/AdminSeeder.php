<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = 'admin@gmail.com';
        if (!Admin::where('email', $adminEmail)->exists()) {
            Admin::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
