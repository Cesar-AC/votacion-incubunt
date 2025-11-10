<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\RolUser;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'idUser' => 1,
            'usuario' => 'admin',
            'email' => 'admin@incubunt.com',
            'password' => Hash::make('admin123'),
        ]);

        RolUser::create([
            'idRol' => 1,
            'idUser' => 1,
        ]);

        $usuarios = [
            [
                'idUser' => 2,
                'usuario' => 'auditor',
                'email' => 'auditor@incubunt.com',
                'password' => Hash::make('auditor123'),
            ],
            [
                'idUser' => 3,
                'usuario' => 'votante1',
                'email' => 'votante1@incubunt.com',
                'password' => Hash::make('votante123'),
            ],
        ];

        foreach ($usuarios as $index => $userData) {
            $user = User::create($userData);
            
            $rolId = $index + 2;
            RolUser::create([
                'idRol' => $rolId,
                'idUser' => $userData['idUser'],
            ]);
        }
    }
}
