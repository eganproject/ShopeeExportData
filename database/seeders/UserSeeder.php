<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buat user
        $users = [
            [
                "name"=> "Otomotif",
                "email"=> "otomotif@admin.com",
                "password"=> bcrypt("bosoto123")
                ],
            ];
            
            foreach ($users as $user) {
                \App\Models\User::create($user);
            }

    }
}
