<?php

namespace App\DTOs\Inventory;

use App\DTOs\BaseDTO;
use App\DTOs\Product\ProductDTO;

/**
 * DTO para resumo consolidado de estoque
 * 
 * Este DTO encapsula os dados consolidados de um produto no estoque,
 * incluindo cálculos de valores totais e margem de lucro.
 * 
 * @package App\DTOs\Inventory
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventorySummaryDTO extends BaseDTO
{
    /**
     * ID do produto
     * 
     * @var int
     */
    public int $product_id;

    /**
     * Dados do produto
     * 
     * @var ProductDTO
     */
    public ProductDTO $product;

    /**
     * Quantidade total em estoque
     * 
     * @var int
     */
    public int $quantity;

    /**
     * Valor total de custo
     * 
     * @var float
     */
    public float $total_cost_value;

    /**
     * Valor total de venda
     * 
     * @var float
     */
    public float $total_sale_value;

    /**
     * Lucro projetado
     * 
     * @var float
     */
    public float $projected_profit;

    /**
     * Margem de lucro percentual
     * 
     * @var float
     */
    public float $profit_margin_percentage;

    /**
     * Data da última atualização
     * 
     * @var string|null
     */
    public ?string $last_updated = null;

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
     * Construtor do DTO de resumo de estoque
     * 
     * @param int $product_id ID do produto
     * @param ProductDTO $product Dados do produto
     * @param int $quantity Quantidade total
     * @param float $total_cost_value Valor total de custo
     * @param float $total_sale_value Valor total de venda
     * @param float $projected_profit Lucro projetado
     * @param float $profit_margin_percentage Margem de lucro
     * @param string|null $last_updated Data da última atualização
     * @param string|null $created_at Data de criação
     * @param string|null $updated_at Data de atualização
     */
    public function __construct(
        int $product_id,
        ProductDTO $product,
        int $quantity,
        float $total_cost_value,
        float $total_sale_value,
        float $projected_profit,
        float $profit_margin_percentage,
        ?string $last_updated = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->product_id = $product_id;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->total_cost_value = $total_cost_value;
        $this->total_sale_value = $total_sale_value;
        $this->projected_profit = $projected_profit;
        $this->profit_margin_percentage = $profit_margin_percentage;
        $this->last_updated = $last_updated;
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
        return new static(
            $data['product_id'],
            ProductDTO::fromArray($data['product']),
            $data['quantity'],
            $data['total_cost_value'],
            $data['total_sale_value'],
            $data['projected_profit'],
            $data['profit_margin_percentage'],
            $data['last_updated'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    /**
     * Calcula os valores automaticamente baseado no produto e quantidade
     * 
     * @param ProductDTO $product Dados do produto
     * @param int $quantity Quantidade em estoque
     * @return static
     */
    public static function calculateFromProduct(ProductDTO $product, int $quantity): static
    {
        $totalCostValue = $quantity * $product->cost_price;
        $totalSaleValue = $quantity * $product->sale_price;
        $projectedProfit = $totalSaleValue - $totalCostValue;
        $profitMargin = $product->getProfitMargin();

        return new static(
            $product->id ?? 0,
            $product,
            $quantity,
            round($totalCostValue, 2),
            round($totalSaleValue, 2),
            round($projectedProfit, 2),
            round($profitMargin, 2)
        );
    }
}
