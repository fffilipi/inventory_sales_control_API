<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\InventoryService;
use App\DTOs\Inventory\InventoryItemDTO;
use App\Respositories\InventoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Testes unitários para InventoryService
 * 
 * Esta classe contém testes unitários para verificar o funcionamento
 * correto do InventoryService, incluindo adição de estoque e consultas.
 * 
 * @package Tests\Unit
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Serviço de estoque para teste
     *
     * @var InventoryService
     */
    private InventoryService $inventoryService;

    /**
     * Repository de estoque mockado
     *
     * @var InventoryRepository
     */
    private InventoryRepository $inventoryRepository;

    /**
     * Configuração inicial dos testes
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->inventoryRepository = new InventoryRepository();
        $this->inventoryService = new InventoryService($this->inventoryRepository);
    }

    /**
     * Testa a adição de estoque para um produto novo
     *
     * @return void
     */
    public function test_add_stock_new_product(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $inventoryData = [
            'product_id' => $product->id,
            'quantity' => 10,
        ];
        
        $inventoryDTO = InventoryItemDTO::fromArray($inventoryData);

        // Act
        $result = $this->inventoryService->addStock($inventoryDTO->toModelData());

        // Assert
        $this->assertInstanceOf(Inventory::class, $result);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(10, $result->quantity);
    }

    /**
     * Testa a adição de estoque para um produto existente
     *
     * @return void
     */
    public function test_add_stock_existing_product(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $existingInventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $inventoryData = [
            'product_id' => $product->id,
            'quantity' => 10,
        ];

        // Act
        $result = $this->inventoryService->addStock($inventoryData);

        // Assert
        $this->assertEquals($existingInventory->id, $result->id);
        $this->assertEquals(15, $result->quantity); // 5 + 10
    }

    /**
     * Testa a adição de estoque em lote
     *
     * @return void
     */
    public function test_add_bulk_stock(): void
    {
        // Arrange
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        
        $bulkData = [
            [
                'product_id' => $product1->id,
                'quantity' => 10,
            ],
            [
                'product_id' => $product2->id,
                'quantity' => 15,
            ],
        ];

        // Act
        $result = $this->inventoryService->addBulkStock($bulkData);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Inventory::class, $result[0]);
        $this->assertInstanceOf(Inventory::class, $result[1]);
    }

    /**
     * Testa a consulta de estoque consolidado
     *
     * @return void
     */
    public function test_get_stock_consolidated(): void
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

        // Act
        $result = $this->inventoryService->getStock();

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(1, $result);
        
        $stockItem = $result->first();
        $this->assertEquals($product->id, $stockItem['product_id']);
        $this->assertEquals(10, $stockItem['quantity']);
        $this->assertEquals(1000.00, $stockItem['total_cost_value']); // 10 * 100
        $this->assertEquals(1500.00, $stockItem['total_sale_value']); // 10 * 150
        $this->assertEquals(500.00, $stockItem['projected_profit']); // 1500 - 1000
    }

    /**
     * Testa a consulta de estoque com múltiplos registros do mesmo produto
     *
     * @return void
     */
    public function test_get_stock_multiple_records_same_product(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'cost_price' => 50.00,
            'sale_price' => 75.00,
        ]);
        
        // Criar múltiplos registros de estoque para o mesmo produto
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
        
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Act
        $result = $this->inventoryService->getStock();

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(1, $result); // Deve agrupar por produto
        
        $stockItem = $result->first();
        $this->assertEquals($product->id, $stockItem['product_id']);
        $this->assertEquals(15, $stockItem['quantity']); // 10 + 5
        $this->assertEquals(750.00, $stockItem['total_cost_value']); // 15 * 50
        $this->assertEquals(1125.00, $stockItem['total_sale_value']); // 15 * 75
        $this->assertEquals(375.00, $stockItem['projected_profit']); // 1125 - 750
    }
}
