<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Services\SalesService;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\InventoryRepositoryInterface;
use App\Events\SaleCompleted;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;

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
    /**
     * Serviço de vendas para teste
     *
     * @var SalesService
     */
    private SalesService $salesService;

    /**
     * Repository de vendas mockado
     *
     * @var SalesRepositoryInterface|MockInterface
     */
    private MockInterface $salesRepositoryMock;

    /**
     * Repository de produtos mockado
     *
     * @var ProductRepositoryInterface|MockInterface
     */
    private MockInterface $productRepositoryMock;

    /**
     * Repository de estoque mockado
     *
     * @var InventoryRepositoryInterface|MockInterface
     */
    private MockInterface $inventoryRepositoryMock;

    /**
     * Configuração inicial dos testes
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar mocks das interfaces dos repositories
        $this->salesRepositoryMock = Mockery::mock(SalesRepositoryInterface::class);
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->inventoryRepositoryMock = Mockery::mock(InventoryRepositoryInterface::class);

        // Instanciar service com mocks
        $this->salesService = new SalesService(
            $this->salesRepositoryMock,
            $this->productRepositoryMock,
            $this->inventoryRepositoryMock
        );

        // Disable events para testes unitários
        Event::fake();
    }

    /**
     * Limpa os mocks após cada teste
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper: Cria um produto mockado para testes
     *
     * @param int $id
     * @param float $costPrice
     * @param float $salePrice
     * @param string $name
     * @return Product
     */
    private function createProductMock(int $id, float $costPrice, float $salePrice, string $name = 'Produto Teste'): Product
    {
        $product = new Product();
        $product->id = $id;
        $product->cost_price = $costPrice;
        $product->sale_price = $salePrice;
        $product->name = $name;
        return $product;
    }

    /**
     * Helper: Cria um estoque mockado para testes
     *
     * @param int $productId
     * @param int $quantity
     * @return Inventory
     */
    private function createInventoryMock(int $productId, int $quantity): Inventory
    {
        $inventory = new Inventory();
        $inventory->product_id = $productId;
        $inventory->quantity = $quantity;
        return $inventory;
    }

    /**
     * Helper: Cria uma venda mockada para testes
     *
     * @param int $id
     * @param float $totalAmount
     * @param float $totalCost
     * @param float $totalProfit
     * @param string $status
     * @return Sale
     */
    private function createSaleMock(int $id, float $totalAmount, float $totalCost, float $totalProfit, string $status): Sale
    {
        $sale = new Sale();
        $sale->id = $id;
        $sale->total_amount = $totalAmount;
        $sale->total_cost = $totalCost;
        $sale->total_profit = $totalProfit;
        $sale->status = $status;
        return $sale;
    }

    /**
     * Helper: Configura o mock de update para atualizar o objeto sale original
     *
     * @param Sale $sale
     * @param Sale $saleUpdated
     * @param array $expectedData
     * @return void
     */
    private function mockSaleUpdate(Sale $sale, Sale $saleUpdated, array $expectedData): void
    {
        $this->salesRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($sale->id, $expectedData)
            ->andReturnUsing(function() use ($sale, $saleUpdated) {
                $sale->total_amount = $saleUpdated->total_amount;
                $sale->total_cost = $saleUpdated->total_cost;
                $sale->total_profit = $saleUpdated->total_profit;
                $sale->status = $saleUpdated->status;
                return $sale;
            });
    }

    /**
     * Testa a criação de uma venda com sucesso
     *
     * @return void
     */
    public function test_create_sale_success(): void
    {
        // Arrange
        $productId = 1;
        $quantity = 2;
        $costPrice = 100.00;
        $salePrice = 150.00;
        $expectedTotalAmount = 300.00; // 2 * 150
        $expectedTotalCost = 200.00; // 2 * 100
        $expectedTotalProfit = 100.00; // 300 - 200

        $product = $this->createProductMock($productId, $costPrice, $salePrice);
        $inventory = $this->createInventoryMock($productId, 10);
        $sale = $this->createSaleMock(1, 0, 0, 0, 'pending');
        $saleUpdated = $this->createSaleMock(1, $expectedTotalAmount, $expectedTotalCost, $expectedTotalProfit, 'completed');

        // Configurar expectativas dos mocks
        $this->inventoryRepositoryMock
            ->shouldReceive('findByProductId')
            ->with($productId)
            ->once()
            ->andReturn($inventory);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($product);

        $this->salesRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with([
                'total_amount' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'pending',
            ])
            ->andReturn($sale);

        $this->salesRepositoryMock
            ->shouldReceive('createItem')
            ->once()
            ->with([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $salePrice,
                'unit_cost' => $costPrice,
            ])
            ->andReturn(new SaleItem());

        $this->mockSaleUpdate($sale, $saleUpdated, [
            'total_amount' => $expectedTotalAmount,
            'total_cost' => $expectedTotalCost,
            'total_profit' => $expectedTotalProfit,
            'status' => 'completed',
        ]);

        $saleData = [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ],
            ],
        ];

        // Act
        $result = $this->salesService->createSale($saleData);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($expectedTotalAmount, $result->total_amount, 'Total amount deve ser calculado corretamente');
        $this->assertEquals($expectedTotalCost, $result->total_cost, 'Total cost deve ser calculado corretamente');
        $this->assertEquals($expectedTotalProfit, $result->total_profit, 'Total profit deve ser calculado corretamente');
        $this->assertEquals('completed', $result->status, 'Status deve ser completed após processamento');
        
        // Verificar se evento foi disparado
        Event::assertDispatched(SaleCompleted::class, function ($event) use ($sale) {
            return $event->sale->id === $sale->id;
        });
    }

    /**
     * Testa a criação de uma venda com múltiplos itens
     *
     * @return void
     */
    public function test_create_sale_multiple_items(): void
    {
        // Arrange
        $product1Id = 1;
        $product2Id = 2;
        $quantity1 = 2;
        $quantity2 = 3;
        $expectedTotalAmount = 525.00; // (2 * 150) + (3 * 75)
        $expectedTotalCost = 350.00; // (2 * 100) + (3 * 50)
        $expectedTotalProfit = 175.00; // 525 - 350

        $product1 = $this->createProductMock($product1Id, 100.00, 150.00, 'Produto 1');
        $product2 = $this->createProductMock($product2Id, 50.00, 75.00, 'Produto 2');
        $inventory1 = $this->createInventoryMock($product1Id, 10);
        $inventory2 = $this->createInventoryMock($product2Id, 10);
        $sale = $this->createSaleMock(1, 0, 0, 0, 'pending');
        $saleUpdated = $this->createSaleMock(1, $expectedTotalAmount, $expectedTotalCost, $expectedTotalProfit, 'completed');

        // Configurar expectativas
        $this->inventoryRepositoryMock
            ->shouldReceive('findByProductId')
            ->with($product1Id)
            ->once()
            ->andReturn($inventory1);

        $this->inventoryRepositoryMock
            ->shouldReceive('findByProductId')
            ->with($product2Id)
            ->once()
            ->andReturn($inventory2);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($product1Id)
            ->once()
            ->andReturn($product1);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($product2Id)
            ->once()
            ->andReturn($product2);

        $this->salesRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($sale);

        $this->salesRepositoryMock
            ->shouldReceive('createItem')
            ->twice()
            ->andReturn(new SaleItem());

        $this->mockSaleUpdate($sale, $saleUpdated, [
            'total_amount' => $expectedTotalAmount,
            'total_cost' => $expectedTotalCost,
            'total_profit' => $expectedTotalProfit,
            'status' => 'completed',
        ]);

        $saleData = [
            'items' => [
                ['product_id' => $product1Id, 'quantity' => $quantity1],
                ['product_id' => $product2Id, 'quantity' => $quantity2],
            ],
        ];

        // Act
        $result = $this->salesService->createSale($saleData);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($expectedTotalAmount, $result->total_amount, 'Total amount deve ser a soma de todos os itens');
        $this->assertEquals($expectedTotalCost, $result->total_cost, 'Total cost deve ser a soma de todos os custos');
        $this->assertEquals($expectedTotalProfit, $result->total_profit, 'Total profit deve ser calculado corretamente');
        $this->assertEquals('completed', $result->status, 'Status deve ser completed após processamento');

    }

    /**
     * Testa a validação de estoque insuficiente
     *
     * @return void
     */
    public function test_create_sale_insufficient_stock(): void
    {
        // Arrange
        $productId = 1;
        $availableQuantity = 5;
        $requestedQuantity = 10;

        $product = $this->createProductMock($productId, 100.00, 150.00);
        $inventory = $this->createInventoryMock($productId, $availableQuantity);

        $this->inventoryRepositoryMock
            ->shouldReceive('findByProductId')
            ->with($productId)
            ->once()
            ->andReturn($inventory);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($product);

        $saleData = [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => $requestedQuantity,
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Estoque insuficiente');
        $this->expectExceptionMessage((string)$availableQuantity);
        $this->expectExceptionMessage((string)$requestedQuantity);
        
        $this->salesService->createSale($saleData);
        
        // Verificar que nenhuma venda foi criada
        $this->salesRepositoryMock->shouldNotHaveReceived('create');
    }

    /**
     * Testa a validação de produto sem estoque
     *
     * @return void
     */
    public function test_create_sale_product_without_stock(): void
    {
        // Arrange
        $productId = 1;
        $product = $this->createProductMock($productId, 100.00, 150.00, 'Produto Sem Estoque');

        $this->inventoryRepositoryMock
            ->shouldReceive('findByProductId')
            ->with($productId)
            ->once()
            ->andReturn(null); // Produto sem estoque

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($product);

        $saleData = [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 1,
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('não possui estoque disponível');
        
        $this->salesService->createSale($saleData);
        
        // Verificar que nenhuma venda foi criada
        $this->salesRepositoryMock->shouldNotHaveReceived('create');
    }

    /**
     * Testa a busca de detalhes de uma venda
     *
     * @return void
     */
    public function test_get_sale_details_success(): void
    {
        // Arrange
        $saleId = 1;
        $sale = new Sale();
        $sale->id = $saleId;
        $sale->status = 'completed';
        $sale->setRelation('items', collect([]));
        $sale->setRelation('items.product', collect([]));

        $this->salesRepositoryMock
            ->shouldReceive('find')
            ->with($saleId)
            ->once()
            ->andReturn($sale);

        // Act
        $result = $this->salesService->getSaleDetails($saleId);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($saleId, $result->id);
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

        $this->salesRepositoryMock
            ->shouldReceive('find')
            ->with($nonExistentId)
            ->once()
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->salesService->getSaleDetails($nonExistentId);
    }
}
