<?php

namespace App\DTOs\Product;

use App\DTOs\BaseDTO;

/**
 * DTO para dados de produto
 * 
 * Este DTO encapsula os dados de um produto, incluindo
 * informações básicas e preços.
 * 
 * @package App\DTOs\Product
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductDTO extends BaseDTO
{
    /**
     * ID do produto
     * 
     * @var int|null
     */
    public ?int $id = null;

    /**
     * SKU do produto
     * 
     * @var string
     */
    public string $sku;

    /**
     * Nome do produto
     * 
     * @var string
     */
    public string $name;

    /**
     * Descrição do produto
     * 
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Preço de custo
     * 
     * @var float
     */
    public float $cost_price;

    /**
     * Preço de venda
     * 
     * @var float
     */
    public float $sale_price;

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
     * Construtor do DTO de produto
     * 
     * @param string $sku SKU do produto
     * @param string $name Nome do produto
     * @param float $cost_price Preço de custo
     * @param float $sale_price Preço de venda
     * @param string|null $description Descrição do produto
     * @param int|null $id ID do produto
     * @param string|null $created_at Data de criação
     * @param string|null $updated_at Data de atualização
     */
    public function __construct(
        string $sku,
        string $name,
        float $cost_price,
        float $sale_price,
        ?string $description = null,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->cost_price = $cost_price;
        $this->sale_price = $sale_price;
        $this->description = $description;
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
        return new static(
            $data['sku'],
            $data['name'],
            $data['cost_price'],
            $data['sale_price'],
            $data['description'] ?? null,
            $data['id'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    /**
     * Cria uma instância do DTO a partir de um modelo Product
     * 
     * @param \App\Models\Product $product Modelo do produto
     * @return static
     */
    public static function fromModel($product): static
    {
        return new static(
            $product->sku,
            $product->name,
            $product->cost_price,
            $product->sale_price,
            $product->description,
            $product->id,
            $product->created_at?->toISOString(),
            $product->updated_at?->toISOString()
        );
    }

    /**
     * Retorna os dados para criação/atualização no banco
     * 
     * @return array
     */
    public function toModelData(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
        ];
    }

    /**
     * Calcula a margem de lucro do produto
     * 
     * @return float
     */
    public function getProfitMargin(): float
    {
        if ($this->sale_price <= 0) {
            return 0;
        }
        
        return (($this->sale_price - $this->cost_price) / $this->sale_price) * 100;
    }

    /**
     * Calcula o valor do lucro por unidade
     * 
     * @return float
     */
    public function getProfitPerUnit(): float
    {
        return $this->sale_price - $this->cost_price;
    }
}
