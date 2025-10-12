<?php

namespace App\DTOs\Sales;

use App\DTOs\BaseDTO;

/**
 * DTO para item de venda
 * 
 * Este DTO encapsula os dados de um item individual em uma venda,
 * incluindo informações do produto, quantidade e preços.
 * 
 * @package App\DTOs\Sales
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleItemDTO extends BaseDTO
{
    /**
     * ID do produto
     * 
     * @var int
     */
    public int $product_id;

    /**
     * Quantidade vendida
     * 
     * @var int
     */
    public int $quantity;

    /**
     * Preço unitário de venda
     * 
     * @var float
     */
    public float $unit_price;

    /**
     * Custo unitário
     * 
     * @var float
     */
    public float $unit_cost;

    /**
     * Construtor do DTO de item de venda
     * 
     * @param int $product_id ID do produto
     * @param int $quantity Quantidade vendida
     * @param float $unit_price Preço unitário de venda
     * @param float $unit_cost Custo unitário
     */
    public function __construct(
        int $product_id,
        int $quantity,
        float $unit_price,
        float $unit_cost
    ) {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
        $this->unit_price = $unit_price;
        $this->unit_cost = $unit_cost;
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data Dados para criar o DTO
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            $data['product_id'],
            $data['quantity'],
            $data['unit_price'] ?? 0,
            $data['unit_cost'] ?? 0
        );
    }

    /**
     * Cria uma instância do DTO a partir de um modelo SaleItem
     * 
     * @param \App\Models\SaleItem $saleItem Modelo do item de venda
     * @return static
     */
    public static function fromModel($saleItem): static
    {
        return new static(
            $saleItem->product_id,
            $saleItem->quantity,
            $saleItem->unit_price,
            $saleItem->unit_cost
        );
    }

    /**
     * Retorna os dados para criação no banco
     * 
     * @param int $sale_id ID da venda
     * @return array
     */
    public function toModelData(int $sale_id): array
    {
        return [
            'sale_id' => $sale_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'unit_cost' => $this->unit_cost,
        ];
    }

    /**
     * Calcula o valor total do item
     * 
     * @return float
     */
    public function getTotalValue(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Calcula o custo total do item
     * 
     * @return float
     */
    public function getTotalCost(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    /**
     * Calcula o lucro do item
     * 
     * @return float
     */
    public function getProfit(): float
    {
        return $this->getTotalValue() - $this->getTotalCost();
    }

    /**
     * Calcula a margem de lucro do item
     * 
     * @return float
     */
    public function getProfitMargin(): float
    {
        if ($this->unit_price <= 0) {
            return 0;
        }
        
        return (($this->unit_price - $this->unit_cost) / $this->unit_price) * 100;
    }
}
