<?php

namespace App\DTOs\Sales;

use App\DTOs\BaseDTO;

/**
 * DTO para dados de venda
 * 
 * Este DTO encapsula os dados de uma venda, incluindo
 * itens, valores totais e informações de lucro.
 * 
 * @package App\DTOs\Sales
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleDTO extends BaseDTO
{
    /**
     * ID da venda
     * 
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Itens da venda
     * 
     * @var SaleItemDTO[]
     */
    public array $items;

    /**
     * Valor total da venda
     * 
     * @var float
     */
    public float $total_amount;

    /**
     * Custo total da venda
     * 
     * @var float
     */
    public float $total_cost;

    /**
     * Lucro total da venda
     * 
     * @var float
     */
    public float $total_profit;

    /**
     * Status da venda
     * 
     * @var string
     */
    public string $status;

    /**
     * Data de criação
     * 
     * @var string|null
     */
    public ?string $created_at = null;

    /**
     * Data de atualização
     * 
     * @var string|null
     */
    public ?string $updated_at = null;

    /**
     * Construtor do DTO de venda
     * 
     * @param SaleItemDTO[] $items Itens da venda
     * @param float $total_amount Valor total
     * @param float $total_cost Custo total
     * @param float $total_profit Lucro total
     * @param string $status Status da venda
     * @param int|null $id ID da venda
     * @param string|null $created_at Data de criação
     * @param string|null $updated_at Data de atualização
     */
    public function __construct(
        array $items,
        float $total_amount,
        float $total_cost,
        float $total_profit,
        string $status = 'completed',
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->items = $items;
        $this->total_amount = $total_amount;
        $this->total_cost = $total_cost;
        $this->total_profit = $total_profit;
        $this->status = $status;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data Dados para criar o DTO
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $items = array_map(function ($item) {
            return SaleItemDTO::fromArray($item);
        }, $data['items'] ?? []);

        return new static(
            $items,
            $data['total_amount'] ?? 0,
            $data['total_cost'] ?? 0,
            $data['total_profit'] ?? 0,
            $data['status'] ?? 'completed',
            $data['id'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    /**
     * Cria uma instância do DTO a partir de um modelo Sale
     * 
     * @param \App\Models\Sale $sale Modelo da venda
     * @return static
     */
    public static function fromModel($sale): static
    {
        $items = $sale->items->map(function ($item) {
            return SaleItemDTO::fromModel($item);
        })->toArray();

        return new static(
            $items,
            $sale->total_amount,
            $sale->total_cost,
            $sale->total_profit,
            $sale->status,
            $sale->id,
            $sale->created_at?->toISOString(),
            $sale->updated_at?->toISOString()
        );
    }

    /**
     * Retorna os dados para criação no banco
     * 
     * @return array
     */
    public function toModelData(): array
    {
        return [
            'total_amount' => $this->total_amount,
            'total_cost' => $this->total_cost,
            'total_profit' => $this->total_profit,
            'status' => $this->status,
        ];
    }

    /**
     * Calcula os valores totais automaticamente baseado nos itens
     * 
     * @return static
     */
    public function calculateTotals(): static
    {
        $totalAmount = 0;
        $totalCost = 0;

        foreach ($this->items as $item) {
            $totalAmount += $item->getTotalValue();
            $totalCost += $item->getTotalCost();
        }

        $this->total_amount = $totalAmount;
        $this->total_cost = $totalCost;
        $this->total_profit = $totalAmount - $totalCost;

        return $this;
    }

    /**
     * Calcula a margem de lucro da venda
     * 
     * @return float
     */
    public function getProfitMargin(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        
        return ($this->total_profit / $this->total_amount) * 100;
    }

    /**
     * Retorna o número total de itens na venda
     * 
     * @return int
     */
    public function getTotalItems(): int
    {
        return array_sum(array_map(function ($item) {
            return $item->quantity;
        }, $this->items));
    }
}
