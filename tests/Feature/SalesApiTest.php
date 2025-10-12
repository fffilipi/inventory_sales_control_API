<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

/**
 * Testes de integração para API de vendas
 * 
 * Esta classe contém testes de integração para verificar o funcionamento
 * completo da API de vendas, incluindo criação e consulta de vendas.
 * 
 * @package Tests\Feature
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SalesApiTest extends TestCase
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
        
        // Não fake events para testes de integração - queremos testar o fluxo completo
    }

    /**
     * Testa a criação de uma venda com dados válidos
     *
     * @return void
     */
    public function test_create_sale_with_valid_data(): void
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
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sales', $saleData);

        // Assert
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'total_amount',
                        'total_cost',
                        'total_profit',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                    'timestamp',
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'total_amount' => 300.00, // 2 * 150
                        'total_cost' => 200.00, // 2 * 100
                        'total_profit' => 100.00, // 300 - 200
                        'status' => 'completed',
                    ],
                ]);

        // Verificar se a venda foi criada no banco
        $this->assertDatabaseHas('sales', [
            'total_amount' => 300.00,
            'total_cost' => 200.00,
            'total_profit' => 100.00,
            'status' => 'completed',
        ]);
    }

    /**
     * Testa a criação de uma venda com múltiplos itens
     *
     * @return void
     */
    public function test_create_sale_with_multiple_items(): void
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
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sales', $saleData);

        // Assert
        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'total_amount' => 525.00, // (2 * 150) + (3 * 75)
                        'total_cost' => 350.00, // (2 * 100) + (3 * 50)
                        'total_profit' => 175.00, // 525 - 350
                        'status' => 'completed',
                    ],
                ]);
    }

    /**
     * Testa a criação de uma venda com estoque insuficiente
     *
     * @return void
     */
    public function test_create_sale_with_insufficient_stock(): void
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

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sales', $saleData);

        // Assert
        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }

    /**
     * Testa a consulta de detalhes de uma venda
     *
     * @return void
     */
    public function test_get_sale_details_success(): void
    {
        // Arrange
        $sale = Sale::factory()->completed()->create();

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/sales/{$sale->id}");

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'total_amount',
                        'total_cost',
                        'total_profit',
                        'status',
                        'items',
                        'created_at',
                        'updated_at',
                    ],
                    'timestamp',
                ])
                ->assertJson(['success' => true]);
    }

    /**
     * Testa a consulta de uma venda inexistente
     *
     * @return void
     */
    public function test_get_sale_details_not_found(): void
    {
        // Arrange
        $nonExistentId = 999;

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/sales/{$nonExistentId}");

        // Assert
        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'timestamp',
                ])
                ->assertJson(['success' => false]);
    }

    /**
     * Testa a criação de uma venda sem autenticação
     *
     * @return void
     */
    public function test_create_sale_without_authentication(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
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
        $response = $this->postJson('/api/sales', $saleData);

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
     * Testa a consulta de venda sem autenticação
     *
     * @return void
     */
    public function test_get_sale_without_authentication(): void
    {
        // Arrange
        $sale = Sale::factory()->completed()->create();

        // Act
        $response = $this->getJson("/api/sales/{$sale->id}");

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
