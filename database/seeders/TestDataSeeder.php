<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

/**
 * Seeder para dados de teste
 * 
 * Este seeder cria 5 produtos específicos com preços definidos
 * para serem utilizados nos testes unitários e de integração.
 * 
 * @package Database\Seeders
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class TestDataSeeder extends Seeder
{
    /**
     * Executa o seeder
     * 
     * @return void
     */
    public function run(): void
    {
        // Só executa em ambiente de desenvolvimento ou teste
        if (!app()->environment(['local', 'testing'])) {
            $this->command->info('TestDataSeeder executado apenas em ambiente local/testing');
            return;
        }
        $testProducts = [
            [
                'sku' => 'TEST001',
                'name' => 'Smartphone Samsung Galaxy S24',
                'description' => 'Smartphone Samsung Galaxy S24 com 128GB de armazenamento',
                'cost_price' => 800.00,
                'sale_price' => 1200.00,
            ],
            [
                'sku' => 'TEST002',
                'name' => 'Notebook Dell Inspiron 15',
                'description' => 'Notebook Dell Inspiron 15 com Intel i5 e 8GB RAM',
                'cost_price' => 1500.00,
                'sale_price' => 2200.00,
            ],
            [
                'sku' => 'TEST003',
                'name' => 'Tablet iPad Air 5',
                'description' => 'Tablet iPad Air 5 com 64GB de armazenamento',
                'cost_price' => 1200.00,
                'sale_price' => 1800.00,
            ],
            [
                'sku' => 'TEST004',
                'name' => 'Fone de Ouvido Sony WH-1000XM4',
                'description' => 'Fone de ouvido sem fio Sony com cancelamento de ruído',
                'cost_price' => 300.00,
                'sale_price' => 450.00,
            ],
            [
                'sku' => 'TEST005',
                'name' => 'Smartwatch Apple Watch Series 9',
                'description' => 'Smartwatch Apple Watch Series 9 com GPS',
                'cost_price' => 600.00,
                'sale_price' => 900.00,
            ],
        ];

        foreach ($testProducts as $productData) {
            $product = Product::create($productData);
            
            // Criar estoque inicial para cada produto
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 50, // Quantidade inicial padrão
                'last_updated' => now(),
            ]);
        }
    }
}
