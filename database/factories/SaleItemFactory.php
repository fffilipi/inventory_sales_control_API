<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para criação de itens de venda de teste
 * 
 * Esta factory gera dados fictícios para itens de venda,
 * incluindo quantidades e preços unitários.
 * 
 * @package Database\Factories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleItemFactory extends Factory
{
    /**
     * Nome do modelo correspondente
     *
     * @var string
     */
    protected $model = SaleItem::class;

    /**
     * Define o estado padrão do modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 100);
        $unitCost = $unitPrice * $this->faker->randomFloat(2, 0.6, 0.8);

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
        ];
    }

    /**
     * Cria um item de venda para uma venda específica
     *
     * @param int $saleId ID da venda
     * @return static
     */
    public function forSale(int $saleId): static
    {
        return $this->state(function (array $attributes) use ($saleId) {
            return [
                'sale_id' => $saleId,
            ];
        });
    }

    /**
     * Cria um item de venda para um produto específico
     *
     * @param int $productId ID do produto
     * @return static
     */
    public function forProduct(int $productId): static
    {
        return $this->state(function (array $attributes) use ($productId) {
            return [
                'product_id' => $productId,
            ];
        });
    }

    /**
     * Cria um item de venda com quantidade específica
     *
     * @param int $quantity Quantidade vendida
     * @return static
     */
    public function withQuantity(int $quantity): static
    {
        return $this->state(function (array $attributes) use ($quantity) {
            return [
                'quantity' => $quantity,
            ];
        });
    }
}
