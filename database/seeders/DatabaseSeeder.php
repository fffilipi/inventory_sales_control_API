<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder principal do sistema
 *
 * Este seeder é responsável por executar todos os outros seeders
 * do sistema, garantindo que os dados iniciais sejam criados
 * na ordem correta. É executado quando o comando
 * `php artisan db:seed` é chamado.
 *
 * @package Database\Seeders
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Executa todos os seeders do sistema
     *
     * Este método chama todos os seeders necessários para
     * popular o banco de dados com dados iniciais, incluindo:
     * - Usuários do sistema (administradores, vendedores, etc.)
     * - Dados de teste (produtos e estoque para desenvolvimento)
     *
     * @return void
     *
     * @example
     * php artisan db:seed
     * php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // Usuários do sistema
            TestDataSeeder::class,  // Dados de teste para desenvolvimento
        ]);
    }
}
