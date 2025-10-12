<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para criação de vendas de teste
 * 
 * Esta factory gera dados fictícios para vendas,
 * incluindo valores totais, custos e status.
 * 
 * @package Database\Factories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleFactory extends Factory
{
    /**
     * Nome do modelo correspondente
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define o estado padrão do modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = $this->faker->randomFloat(2, 50, 500);
        $totalCost = $totalAmount * $this->faker->randomFloat(2, 0.6, 0.8);
        $totalProfit = $totalAmount - $totalCost;

        return [
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'status' => $this->faker->randomElement(['pending', 'completed']),
        ];
    }

    /**
     * Cria uma venda com status completed
     *
     * @return static
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Cria uma venda com status pending
     *
     * @return static
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }
}
