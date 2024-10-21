<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'is_admin' => 1,
            'city' => 'Los Angeles',
            'phone_number' => '+1 (111) 111-1111',
            'password' => \Hash::make('adminPassword'),
        ]);
    }
}
