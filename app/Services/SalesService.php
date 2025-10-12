<?php

namespace App\Services;

use App\Interfaces\SalesServiceInterface;
use App\Respositories\SalesRepository;
use App\Events\SaleCompleted;
use App\Models\SaleItem;
use App\Models\Product;

/**
 * Serviço responsável pela lógica de negócio das vendas
 * 
 * Este serviço implementa a camada de lógica de negócio para processamento de vendas,
 * incluindo validação de estoque, cálculo de valores e atualização automática do estoque.
 * Centraliza as regras de negócio relacionadas às vendas.
 * 
 * @package App\Services
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SalesService implements SalesServiceInterface
{
    /**
     * Repository de vendas injetado via dependência
     * 
     * @var SalesRepository
     */
    protected $repository;

    /**
     * Construtor do serviço
     * 
     * @param SalesRepository $repository Repository de vendas
     */
    public function __construct(SalesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Processa uma nova venda no sistema
     * 
     * Valida disponibilidade de estoque, calcula valores totais,
     * cria os itens da venda e dispara evento para atualização do estoque.
     * 
     * @param array $data Dados da venda contendo array de itens
     * @return \App\Models\Sale Venda processada
     * @throws \Exception Quando há estoque insuficiente ou erro no processamento
     */
    public function createSale(array $data)
    {
        // Primeiro, valida se há estoque suficiente para todos os itens
        $this->validateStockAvailability($data['items']);

        $totalCost = 0;
        $totalAmount = 0;

        $sale = $this->repository->create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            $unitCost = $product->cost_price;
            $unitPrice = $product->sale_price;

            $totalCost += $unitCost * $item['quantity'];
            $totalAmount += $unitPrice * $item['quantity'];

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
            ]);
        }

        $sale->update([
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalAmount - $totalCost,
            'status' => 'completed',
        ]);

        // Dispara evento para atualizar estoque
        SaleCompleted::dispatch($sale);

        return $sale;
    }

    /**
     * Valida se há estoque suficiente para todos os itens da venda
     * 
     * Verifica se cada produto possui estoque disponível e se a quantidade
     * solicitada não excede o estoque atual.
     * 
     * @param array $items Array de itens da venda
     * @throws \Exception Quando há estoque insuficiente ou produto sem estoque
     */
    private function validateStockAvailability(array $items)
    {
        foreach ($items as $item) {
            $inventory = \App\Models\Inventory::where('product_id', $item['product_id'])->first();
            
            if (!$inventory) {
                $product = Product::findOrFail($item['product_id']);
                throw new \Exception("Produto '{$product->name}' não possui estoque disponível.");
            }

            if ($inventory->quantity < $item['quantity']) {
                $product = Product::findOrFail($item['product_id']);
                throw new \Exception("Estoque insuficiente para o produto '{$product->name}'. Disponível: {$inventory->quantity}, Solicitado: {$item['quantity']}");
            }
        }
    }

    /**
     * Busca os detalhes de uma venda específica
     * 
     * @param int $id ID da venda
     * @return \App\Models\Sale Venda com itens relacionados
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando venda não existe
     */
    public function getSaleDetails(int $id)
    {
        return $this->repository->find($id);
    }
}
