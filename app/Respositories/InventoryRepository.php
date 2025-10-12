<?php

namespace App\Respositories;

use App\Interfaces\InventoryRepositoryInterface;
use App\Models\Inventory;

/**
 * Repository responsável pelo acesso aos dados de estoque
 * 
 * Este repository implementa a camada de acesso a dados para estoque,
 * fornecendo métodos para operações CRUD e cálculos dinâmicos de valores.
 * 
 * @package App\Respositories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryRepository implements InventoryRepositoryInterface
{
    /**
     * Retorna o estoque consolidado com cálculos dinâmicos
     * 
     * Agrupa produtos por ID, soma quantidades e calcula automaticamente:
     * - Valor total de custo
     * - Valor total de venda
     * - Lucro projetado
     * - Margem de lucro percentual
     * 
     * @return \Illuminate\Support\Collection Estoque consolidado
     */
    public function getAll()
    {
        // Busca todos os registros de inventário com produtos
        $inventories = Inventory::with('product')->get();
        
        // Agrupa por product_id e soma as quantidades
        $grouped = $inventories->groupBy('product_id')->map(function ($group) {
            $firstItem = $group->first();
            $product = $firstItem->product;
            
            // Soma todas as quantidades do mesmo produto
            $totalQuantity = $group->sum('quantity');
            
            // Cálculos dinâmicos baseados na quantidade total
            $totalCostValue = $totalQuantity * $product->cost_price;
            $totalSaleValue = $totalQuantity * $product->sale_price;
            $projectedProfit = $totalSaleValue - $totalCostValue;
            $profitMargin = $product->sale_price > 0 ? (($product->sale_price - $product->cost_price) / $product->sale_price) * 100 : 0;
            
            return [
                'product_id' => $firstItem->product_id,
                'product' => [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'cost_price' => $product->cost_price,
                    'sale_price' => $product->sale_price,
                ],
                'quantity' => $totalQuantity,
                'total_cost_value' => round($totalCostValue, 2),
                'total_sale_value' => round($totalSaleValue, 2),
                'projected_profit' => round($projectedProfit, 2),
                'profit_margin_percentage' => round($profitMargin, 2),
                'last_updated' => $group->max('updated_at'),
                'created_at' => $group->min('created_at'),
                'updated_at' => $group->max('updated_at'),
            ];
        });
        
        return $grouped->values(); // Retorna como array indexado
    }

    /**
     * Cria ou atualiza um registro de estoque
     * 
     * Se o produto já existe no estoque, soma a quantidade.
     * Se não existe, cria um novo registro de estoque.
     * 
     * @param array $data Dados do estoque (product_id, quantity)
     * @return \App\Models\Inventory Registro de estoque criado/atualizado
     * @throws \Exception Quando ocorre erro na operação
     */
    public function create(array $data)
    {
        // Verifica se já existe um registro para este produto
        $existingInventory = Inventory::where('product_id', $data['product_id'])->first();
        
        if ($existingInventory) {
            // Se existe, soma a quantidade
            $existingInventory->quantity += $data['quantity'];
            $existingInventory->last_updated = now();
            $existingInventory->save();
            
            return $existingInventory;
        } else {
            // Se não existe, cria um novo registro
            $data['last_updated'] = now();
            return Inventory::create($data);
        }
    }

    /**
     * Busca um registro de estoque específico por ID
     * 
     * @param int $id ID do registro de estoque
     * @return \App\Models\Inventory|null Registro de estoque encontrado ou null
     */
    public function find(int $id)
    {
        return Inventory::find($id);
    }
}
