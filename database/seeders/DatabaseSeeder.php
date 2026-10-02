<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['username' => env('BACKUP_LOGIN_USERNAME', 'admin')],
            [
                'name' => 'Backup Admin',
                'password' => env('BACKUP_LOGIN_PASSWORD', 'password'),
            ],
        );
    }
}
