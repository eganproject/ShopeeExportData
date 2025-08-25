<?php

namespace Database\Seeders;

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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // panggil user seeder
        User::create([
             "name"=> "Otomotif",
                "email"=> "otomotif@gmail.com",
                "password"=> Hash::make("Bosoto123")
        ]);
        User::create([
             "name"=> "Otomotif",
                "email"=> "automotive@gmail.com",
                "password"=> Hash::make("Password!2")
        ]);
        User::create([
             "name"=> "Otomotif",
                "email"=> "ega.dev@gmail.com",
                "password"=> Hash::make("Password!2")
        ]);

    }
}
