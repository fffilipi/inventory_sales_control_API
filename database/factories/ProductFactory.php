<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para criação de produtos de teste
 * 
 * Esta factory gera dados fictícios para produtos, incluindo
 * SKUs únicos, nomes, descrições e preços realistas.
 * 
 * @package Database\Factories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductFactory extends Factory
{
    /**
     * Nome do modelo correspondente
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define o estado padrão do modelo
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);
        $salePrice = $costPrice + $this->faker->randomFloat(2, 5, 50);

        return [
            'sku' => 'PROD' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'cost_price' => $costPrice,
            'sale_price' => $salePrice,
        ];
    }

    /**
     * Cria um produto com margem de lucro específica
     *
     * @param float $marginPercent Margem de lucro em percentual
     * @return static
     */
    public function withMargin(float $marginPercent): static
    {
        return $this->state(function (array $attributes) use ($marginPercent) {
            $costPrice = $attributes['cost_price'] ?? $this->faker->randomFloat(2, 10, 100);
            $salePrice = $costPrice * (1 + $marginPercent / 100);

            return [
                'cost_price' => $costPrice,
                'sale_price' => round($salePrice, 2),
            ];
        });
    }

    /**
     * Cria um produto com preços específicos
     *
     * @param float $costPrice Preço de custo
     * @param float $salePrice Preço de venda
     * @return static
     */
    public function withPrices(float $costPrice, float $salePrice): static
    {
        return $this->state(function (array $attributes) use ($costPrice, $salePrice) {
            return [
                'cost_price' => $costPrice,
                'sale_price' => $salePrice,
            ];
        });
    }
}
