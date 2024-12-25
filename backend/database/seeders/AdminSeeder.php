<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Enums\AdminRole;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@tekiplanet.com',
            'password' => bcrypt('password'),
            'role' => AdminRole::SUPER_ADMIN,
            'is_active' => true
        ]);
    }
} 