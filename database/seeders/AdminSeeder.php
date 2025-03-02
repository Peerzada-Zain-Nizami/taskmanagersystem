<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Agar ye email already exist kare to update ho
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin786'),
                'role' => 'admin',
                'is_approved' => true,
            ]
        );
    }
}
