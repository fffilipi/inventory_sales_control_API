<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para criação de usuários do sistema
 *
 * Este seeder cria os usuários iniciais necessários para o funcionamento
 * do sistema de controle de estoque e vendas, incluindo administradores,
 * vendedores e usuários de teste para desenvolvimento.
 *
 * @package Database\Seeders
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class UserSeeder extends Seeder
{
    /**
     * Executa o seeder de usuários
     *
     * Cria os usuários iniciais do sistema com diferentes perfis:
     * - Administrador: Acesso total ao sistema
     * - Usuário de Teste: Para desenvolvimento e testes
     * - Vendedor: Para operações de venda
     *
     * @return void
     *
     * @example
     * php artisan db:seed --class=UserSeeder
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