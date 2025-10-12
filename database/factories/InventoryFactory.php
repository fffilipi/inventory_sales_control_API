<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para criação de registros de estoque de teste
 * 
 * Esta factory gera dados fictícios para registros de estoque,
 * incluindo quantidades e datas de atualização.
 * 
 * @package Database\Factories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryFactory extends Factory
{
    /**
     * Nome do modelo correspondente
     *
     * @var string
     */
    protected $model = Inventory::class;

    /**
     * Define o estado padrão do modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'last_updated' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Cria um registro de estoque com quantidade específica
     *
     * @param int $quantity Quantidade em estoque
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

    /**
     * Cria um registro de estoque para um produto específico
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
}
