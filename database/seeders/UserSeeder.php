<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('password123'),
        ]);

        // Criar usuário de teste
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@inventory.com',
            'password' => Hash::make('teste123'),
        ]);

        // Criar usuário vendedor
        User::create([
            'name' => 'Vendedor',
            'email' => 'vendedor@inventory.com',
            'password' => Hash::make('vendedor123'),
        ]);
    }
}