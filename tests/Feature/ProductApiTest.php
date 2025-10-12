<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * Testes de integração para API de produtos
 * 
 * Esta classe contém testes de integração para verificar o funcionamento
 * completo da API de produtos, incluindo autenticação e validações.
 * 
 * @package Tests\Feature
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Usuário autenticado para os testes
     *
     * @var User
     */
    private User $user;

    /**
     * Token de autenticação
     *
     * @var string
     */
    private string $token;

    /**
     * Configuração inicial dos testes
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário e token para autenticação
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Testa a listagem de produtos com autenticação
     *
     * @return void
     */
    public function test_get_products_with_authentication(): void
    {
        // Arrange
        Product::factory()->count(3)->create();

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/products');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'sku',
                            'name',
                            'description',
                            'cost_price',
                            'sale_price',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'timestamp',
                ])
                ->assertJson(['success' => true]);
    }

    /**
     * Testa a listagem de produtos sem autenticação
     *
     * @return void
     */
    public function test_get_products_without_authentication(): void
    {
        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }

    /**
     * Testa a criação de um produto com dados válidos
     *
     * @return void
     */
    public function test_create_product_with_valid_data(): void
    {
        // Arrange
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'sku',
                        'name',
                        'description',
                        'cost_price',
                        'sale_price',
                        'created_at',
                        'updated_at',
                    ],
                    'timestamp',
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'sku' => $productData['sku'],
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'cost_price' => $productData['cost_price'],
                        'sale_price' => $productData['sale_price'],
                    ],
                ]);

        // Verificar se o produto foi criado no banco
        $this->assertDatabaseHas('products', [
            'sku' => $productData['sku'],
            'name' => $productData['name'],
        ]);
    }

    /**
     * Testa a criação de um produto com dados inválidos
     *
     * @return void
     */
    public function test_create_product_with_invalid_data(): void
    {
        // Arrange
        $invalidData = [
            'sku' => '', // SKU vazio
            'name' => '', // Nome vazio
            'cost_price' => -10, // Preço negativo
            'sale_price' => 'invalid', // Preço inválido
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', $invalidData);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }

    /**
     * Testa a criação de um produto com SKU duplicado
     *
     * @return void
     */
    public function test_create_product_with_duplicate_sku(): void
    {
        // Arrange
        $existingProduct = Product::factory()->create(['sku' => 'DUPLICATE001']);
        
        $productData = [
            'sku' => 'DUPLICATE001', // SKU duplicado
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }

    /**
     * Testa a criação de um produto sem autenticação
     *
     * @return void
     */
    public function test_create_product_without_authentication(): void
    {
        // Arrange
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto teste',
            'cost_price' => 100.00,
            'sale_price' => 150.00,
        ];

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }
}
