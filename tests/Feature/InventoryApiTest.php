<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * Testes de integração para API de estoque
 * 
 * Esta classe contém testes de integração para verificar o funcionamento
 * completo da API de estoque, incluindo adição individual e em lote.
 * 
 * @package Tests\Feature
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryApiTest extends TestCase
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
     * Testa a consulta de estoque com autenticação
     *
     * @return void
     */
    public function test_get_inventory_with_authentication(): void
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
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'product_id',
                            'product' => [
                                'sku',
                                'name',
                                'description',
                                'cost_price',
                                'sale_price',
                            ],
                            'quantity',
                            'total_cost_value',
                            'total_sale_value',
                            'projected_profit',
                            'profit_margin_percentage',
                            'last_updated',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'timestamp',
                ])
                ->assertJson(['success' => true]);
    }

    /**
     * Testa a consulta de estoque sem autenticação
     *
     * @return void
     */
    public function test_get_inventory_without_authentication(): void
    {
        // Act
        $response = $this->getJson('/api/inventory');

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
     * Testa a adição de estoque individual com dados válidos
     *
     * @return void
     */
    public function test_add_single_inventory_with_valid_data(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
        $inventoryData = [
            'product_id' => $product->id,
            'quantity' => 10,
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/inventory', $inventoryData);

        // Assert
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'timestamp',
                ])
                ->assertJson(['success' => true]);

        // Verificar se o estoque foi criado no banco
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Testa a adição de estoque em lote com dados válidos
     *
     * @return void
     */
    public function test_add_bulk_inventory_with_valid_data(): void
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
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/inventory', $bulkData);

        // Assert
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'timestamp',
                ])
                ->assertJson(['success' => true]);

        // Verificar se os estoques foram criados no banco
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product1->id,
            'quantity' => 10,
        ]);
        
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product2->id,
            'quantity' => 15,
        ]);
    }

    /**
     * Testa a adição de estoque para produto existente (deve somar)
     *
     * @return void
     */
    public function test_add_inventory_existing_product_sums_quantity(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
        Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
        
        $inventoryData = [
            'product_id' => $product->id,
            'quantity' => 10,
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/inventory', $inventoryData);

        // Assert
        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        // Verificar se a quantidade foi somada
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => 15, // 5 + 10
        ]);
    }

    /**
     * Testa a adição de estoque com dados inválidos
     *
     * @return void
     */
    public function test_add_inventory_with_invalid_data(): void
    {
        // Arrange
        $invalidData = [
            'product_id' => 999, // Produto inexistente
            'quantity' => -5, // Quantidade negativa
        ];

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/inventory', $invalidData);

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
     * Testa a adição de estoque sem autenticação
     *
     * @return void
     */
    public function test_add_inventory_without_authentication(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
        $inventoryData = [
            'product_id' => $product->id,
            'quantity' => 10,
        ];

        // Act
        $response = $this->postJson('/api/inventory', $inventoryData);

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
