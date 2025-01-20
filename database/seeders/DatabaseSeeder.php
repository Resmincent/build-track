<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'password' => '@admin123',
            'email' => 'admin@superadmin.com',
            'is_admin' => true,
        ]);
    }
}
