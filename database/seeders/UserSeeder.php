<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'admin',
            'last_name' => 'administrator',
            'phone' => '123456789',
            'role_id' => 1,
            'email' => 'admin@gmail.com',
            'address'=>"address234sree4",
            'password' => bcrypt('12345678'),
            'email_verified' => true,
            'phone_verified' => true,
            'user_verified' => true,
            'status'=>"active",
            'slug'=>"admin",
        ]);
        User::create([
            'first_name' => 'test',
            'last_name' => 'client',
            'phone' => '123456789',
            'address'=>"address234sree4",
            'role_id' => 2,
            'email' => 'client@gmail.com',
            'password' => bcrypt('12345678'),
            'email_verified' => true,
            'phone_verified' => true,
            'user_verified' => true,
            'status'=>"active",
            'slug'=>"client",
        ]);
    }
}
