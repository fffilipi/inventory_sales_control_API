<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Services\ProductService;
use App\DTOs\Product\ProductDTO;
use App\Respositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

/**
 * Testes unitários para ProductService
 * 
 * Esta classe contém testes unitários para verificar o funcionamento
 * correto do ProductService, incluindo criação, listagem e busca de produtos.
 * 
 * @package Tests\Unit
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Serviço de produtos para teste
     *
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * Repository de produtos mockado
     *
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * Configuração inicial dos testes
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productRepository = new ProductRepository();
        $this->productService = new ProductService($this->productRepository);
    }

    /**
     * Testa a criação de um produto com sucesso
     *
     * @return void
     */
    public function test_create_product_success(): void
    {
        // Arrange
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];
        
        $productDTO = ProductDTO::fromArray($productData);

        // Act
        $result = $this->productService->createProduct($productDTO);

        // Assert
        $this->assertInstanceOf(ProductDTO::class, $result);
        $this->assertEquals($productData['sku'], $result->sku);
        $this->assertEquals($productData['name'], $result->name);
        $this->assertEquals($productData['cost_price'], $result->cost_price);
        $this->assertEquals($productData['sale_price'], $result->sale_price);
        $this->assertNotNull($result->id);
    }

    /**
     * Testa a listagem de todos os produtos
     *
     * @return void
     */
    public function test_get_all_products_success(): void
    {
        // Arrange
        Product::factory()->count(3)->create();

        // Act
        $result = $this->productService->getAllProducts();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(ProductDTO::class, $result[0]);
    }

    /**
     * Testa a busca de um produto por ID
     *
     * @return void
     */
    public function test_get_product_by_id_success(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->productService->getProduct($product->id);

        // Assert
        $this->assertInstanceOf(ProductDTO::class, $result);
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals($product->sku, $result->sku);
        $this->assertEquals($product->name, $result->name);
    }

    /**
     * Testa a busca de um produto inexistente
     *
     * @return void
     */
    public function test_get_product_by_id_not_found(): void
    {
        // Arrange
        $nonExistentId = 999;

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->productService->getProduct($nonExistentId);
    }

    /**
     * Testa o cálculo de margem de lucro do produto
     *
     * @return void
     */
    public function test_product_profit_margin_calculation(): void
    {
        // Arrange
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];
        
        $productDTO = ProductDTO::fromArray($productData);

        // Act
        $profitMargin = $productDTO->getProfitMargin();

        // Assert
        $expectedMargin = (($productData['sale_price'] - $productData['cost_price']) / $productData['sale_price']) * 100;
        $this->assertEquals($expectedMargin, $profitMargin);
    }

    /**
     * Testa o cálculo de lucro por unidade
     *
     * @return void
     */
    public function test_product_profit_per_unit_calculation(): void
    {
        // Arrange
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];
        
        $productDTO = ProductDTO::fromArray($productData);

        // Act
        $profitPerUnit = $productDTO->getProfitPerUnit();

        // Assert
        $expectedProfit = $productData['sale_price'] - $productData['cost_price'];
        $this->assertEquals($expectedProfit, $profitPerUnit);
    }
}
