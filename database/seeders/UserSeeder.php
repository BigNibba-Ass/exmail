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
            'password' => \Hash::make('adminPassword'),
        ]);
    }
}
