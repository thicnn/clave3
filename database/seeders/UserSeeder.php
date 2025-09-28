<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear o actualizar usuario administrador
        User::updateOrCreate(
            ['email' => 'thicun04@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Thicun0867'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario empleado
        User::updateOrCreate(
            ['email' => 'impresiones@clavetres.com'],
            [
                'name' => 'Empleado',
                'password' => Hash::make('clave3'),
                'role' => 'empleado',
                'email_verified_at' => now(),
            ]
        );
    }
}