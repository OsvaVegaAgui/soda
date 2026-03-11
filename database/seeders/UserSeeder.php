<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            // Usuarios básicos (rol 2)
            ['name' => 'Yuliana Zamora Solís',      'email' => 'yzamora@caep.co.cr',       'password' => 'admin',  'rol' => 2],
            ['name' => 'Yeisy María Arce Alfaro',    'email' => 'yarce@caep.co.cr',          'password' => 'admin',  'rol' => 2],
            ['name' => 'Funcionaria1',               'email' => 'funcionaria1@caep.co.cr',   'password' => 'admin',  'rol' => 2],
            ['name' => 'Funcionaria2',               'email' => 'funcionaria2@caep.co.cr',   'password' => 'admin',  'rol' => 2],
            // Admin (rol 1)
            ['name' => 'Osvaldo Vega Aguilar',       'email' => 'ovega@etai.acr.cr',         'password' => '123456', 'rol' => 1],
        ];

        foreach ($usuarios as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'rol'      => $data['rol'],
                    'password' => Hash::make($data['password']),
                    'activo'   => true,
                ]
            );
        }
    }
}
