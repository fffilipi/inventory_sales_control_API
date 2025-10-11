<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'sku' => 'PROD001',
                'name' => 'Produto A',
                'description' => 'Descrição do Produto A',
                'cost_price' => 10.00,
                'sale_price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD002',
                'name' => 'Produto B',
                'description' => 'Descrição do Produto B',
                'cost_price' => 20.00,
                'sale_price' => 30.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD003',
                'name' => 'Produto C',
                'description' => 'Descrição do Produto C',
                'cost_price' => 15.00,
                'sale_price' => 25.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD004',
                'name' => 'Produto D',
                'description' => 'Descrição do Produto D',
                'cost_price' => 12.50,
                'sale_price' => 18.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD005',
                'name' => 'Produto E',
                'description' => 'Descrição do Produto E',
                'cost_price' => 8.00,
                'sale_price' => 12.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
