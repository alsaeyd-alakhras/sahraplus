<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Constant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Admin User
        Admin::create([
            'name'=> 'Administrator',
            'username'=> 'admin',
            'email'=> 'admin@admin.com',
            'password'=> Hash::make('12345678'),
            'last_activity'  => now(),
            'avatar'  => null,
            'super_admin'  => 1,
            'is_active' => 1,
        ]);


        User::create([
            'first_name' => 'User',
            'last_name' => null,
            'email' => 'user@gmail.com',
            'phone' => null,
            'password' => Hash::make('12345678'),
            'date_of_birth' => null,
            'gender' => 'male',
            'country_code' => null,
            'avatar' => null,
            'last_activity' => now()
        ]);


        if (app()->environment('local')) {
            $this->call(ContentSeeder::class);
        }
    }
}
