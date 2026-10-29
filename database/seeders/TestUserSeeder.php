<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'usuario@gmail.com'],
            [
                'name' => 'Administrador de Prueba',
                'password' => Hash::make('1234'),
            ]
        );
    }
}

