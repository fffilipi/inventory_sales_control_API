<?php

namespace App\Services;

use App\Interfaces\SalesServiceInterface;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\InventoryRepositoryInterface;
use App\Events\SaleCompleted;

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
     * @var SalesRepositoryInterface
     */
    protected $salesRepository;

    /**
     * Repository de produtos injetado via dependência
     * 
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Repository de estoque injetado via dependência
     * 
     * @var InventoryRepositoryInterface
     */
    protected $inventoryRepository;

    /**
     * Construtor do serviço
     * 
     * @param SalesRepositoryInterface $salesRepository Repository de vendas
     * @param ProductRepositoryInterface $productRepository Repository de produtos
     * @param InventoryRepositoryInterface $inventoryRepository Repository de estoque
     */
    public function __construct(
        SalesRepositoryInterface $salesRepository,
        ProductRepositoryInterface $productRepository,
        InventoryRepositoryInterface $inventoryRepository
    ) {
        $this->salesRepository = $salesRepository;
        $this->productRepository = $productRepository;
        $this->inventoryRepository = $inventoryRepository;
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

        $sale = $this->salesRepository->create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        foreach ($data['items'] as $item) {
            $product = $this->productRepository->find($item['product_id']);

            $unitCost = $product->cost_price;
            $unitPrice = $product->sale_price;

            $totalCost += $unitCost * $item['quantity'];
            $totalAmount += $unitPrice * $item['quantity'];

            $this->salesRepository->createItem([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
            ]);
        }

        $this->salesRepository->update($sale->id, [
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalAmount - $totalCost,
            'status' => 'completed',
        ]);

        $sale->refresh();

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
            $inventory = $this->inventoryRepository->findByProductId($item['product_id']);
            
            if (!$inventory) {
                $product = $this->productRepository->find($item['product_id']);
                throw new \Exception("Produto '{$product->name}' não possui estoque disponível.");
            }

            if ($inventory->quantity < $item['quantity']) {
                $product = $this->productRepository->find($item['product_id']);
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
        return $this->salesRepository->find($id);
    }
}
