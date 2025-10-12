<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Inventory;
use App\Services\SalesService;
use App\DTOs\Sales\SaleDTO;
use App\Respositories\SalesRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

/**
 * Testes unitários para SalesService
 * 
 * Esta classe contém testes unitários para verificar o funcionamento
 * correto do SalesService, incluindo criação de vendas e validações.
 * 
 * @package Tests\Unit
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SalesServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Serviço de vendas para teste
     *
     * @var SalesService
     */
    private SalesService $salesService;

    /**
     * Repository de vendas mockado
     *
     * @var SalesRepository
     */
    private SalesRepository $salesRepository;

    /**
     * Configuração inicial dos testes
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->salesRepository = new SalesRepository(new Sale());
        $this->salesService = new SalesService($this->salesRepository);
        
        // Disable events para testes unitários
        Event::fake();
    }

    /**
     * Testa a criação de uma venda com sucesso
     *
     * @return void
     */
    public function test_create_sale_success(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ]);
        
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $saleData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        // Act
        $result = $this->salesService->createSale($saleData);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals(300.00, $result->total_amount); // 2 * 150
        $this->assertEquals(200.00, $result->total_cost); // 2 * 100
        $this->assertEquals(100.00, $result->total_profit); // 300 - 200
        $this->assertEquals('completed', $result->status);
    }

    /**
     * Testa a criação de uma venda com múltiplos itens
     *
     * @return void
     */
    public function test_create_sale_multiple_items(): void
    {
        // Arrange
        $product1 = Product::factory()->create([
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ]);
        
        $product2 = Product::factory()->create([
            'cost_price' => 50.00,
            'sale_price' => 75.00,
        ]);
        
        Inventory::factory()->create([
            'product_id' => $product1->id,
            'quantity' => 10,
        ]);
        
        Inventory::factory()->create([
            'product_id' => $product2->id,
            'quantity' => 10,
        ]);

        $saleData = [
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 3,
                ],
            ],
        ];

        // Act
        $result = $this->salesService->createSale($saleData);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals(525.00, $result->total_amount); // (2 * 150) + (3 * 75)
        $this->assertEquals(350.00, $result->total_cost); // (2 * 100) + (3 * 50)
        $this->assertEquals(175.00, $result->total_profit); // 525 - 350
        $this->assertEquals('completed', $result->status);
    }

    /**
     * Testa a validação de estoque insuficiente
     *
     * @return void
     */
    public function test_create_sale_insufficient_stock(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $saleData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10, // Mais que o disponível
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Estoque insuficiente');
        
        $this->salesService->createSale($saleData);
    }

    /**
     * Testa a validação de produto sem estoque
     *
     * @return void
     */
    public function test_create_sale_product_without_stock(): void
    {
        // Arrange
        $product = Product::factory()->create();

        $saleData = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('não possui estoque disponível');
        
        $this->salesService->createSale($saleData);
    }

    /**
     * Testa a busca de detalhes de uma venda
     *
     * @return void
     */
    public function test_get_sale_details_success(): void
    {
        // Arrange
        $sale = Sale::factory()->completed()->create();

        // Act
        $result = $this->salesService->getSaleDetails($sale->id);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($sale->id, $result->id);
        $this->assertTrue($result->relationLoaded('items'));
    }

    /**
     * Testa a busca de uma venda inexistente
     *
     * @return void
     */
    public function test_get_sale_details_not_found(): void
    {
        // Arrange
        $nonExistentId = 999;

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->salesService->getSaleDetails($nonExistentId);
    }
}
