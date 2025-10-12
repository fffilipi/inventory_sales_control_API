<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Database\Seeders\TestDataSeeder;

/**
 * Teste de integração completo do fluxo de trabalho
 * 
 * Este teste verifica todo o fluxo de trabalho da aplicação,
 * desde a criação de produtos até a realização de vendas,
 * usando os dados mocados dos 5 produtos de teste.
 * 
 * @package Tests\Feature
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class CompleteWorkflowTest extends TestCase
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
        
        // Executar seeder com dados de teste
        $this->seed(TestDataSeeder::class);
    }

    /**
     * Testa o fluxo completo: produtos -> estoque -> vendas
     *
     * @return void
     */
    public function test_complete_workflow_from_products_to_sales(): void
    {
        // 1. Verificar se os produtos de teste foram criados
        $this->assertDatabaseCount('products', 5);
        $this->assertDatabaseCount('inventory', 5);

        // 2. Consultar produtos via API
        $productsResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/products');

        $productsResponse->assertStatus(200)
                        ->assertJson(['success' => true])
                        ->assertJsonCount(5, 'data');

        // 3. Consultar estoque via API
        $inventoryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        $inventoryResponse->assertStatus(200)
                         ->assertJson(['success' => true])
                         ->assertJsonCount(5, 'data');

        // Verificar se os cálculos estão corretos
        $inventoryData = $inventoryResponse->json('data');
        foreach ($inventoryData as $item) {
            $this->assertEquals(50, $item['quantity']); // Quantidade inicial
            $this->assertGreaterThan(0, $item['total_cost_value']);
            $this->assertGreaterThan(0, $item['total_sale_value']);
            $this->assertGreaterThan(0, $item['projected_profit']);
        }

        // 4. Realizar uma venda com múltiplos produtos
        $products = Product::all();
        $saleData = [
            'items' => [
                [
                    'product_id' => $products[0]->id, // Smartphone
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[1]->id, // Notebook
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[2]->id, // Tablet
                    'quantity' => 2,
                ],
            ],
        ];

        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sales', $saleData);

        $saleResponse->assertStatus(201)
                    ->assertJson(['success' => true]);

        // Verificar valores da venda
        $saleData = $saleResponse->json('data');
        $expectedTotal = 1200.00 + 2200.00 + (2 * 1800.00); // 7000.00
        $expectedCost = 800.00 + 1500.00 + (2 * 1200.00); // 4700.00
        $expectedProfit = $expectedTotal - $expectedCost; // 2300.00

        $this->assertEquals($expectedTotal, $saleData['total_amount']);
        $this->assertEquals($expectedCost, $saleData['total_cost']);
        $this->assertEquals($expectedProfit, $saleData['total_profit']);

        // 5. Consultar detalhes da venda
        $saleDetailsResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/sales/{$saleData['id']}");

        $saleDetailsResponse->assertStatus(200)
                           ->assertJson(['success' => true])
                           ->assertJsonCount(3, 'data.items');

        // 6. Verificar se o estoque foi atualizado
        $updatedInventoryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        $updatedInventoryData = $updatedInventoryResponse->json('data');
        
        // Encontrar os produtos vendidos e verificar quantidades
        foreach ($updatedInventoryData as $item) {
            if ($item['product_id'] == $products[0]->id) {
                $this->assertEquals(49, $item['quantity']); // 50 - 1
            } elseif ($item['product_id'] == $products[1]->id) {
                $this->assertEquals(49, $item['quantity']); // 50 - 1
            } elseif ($item['product_id'] == $products[2]->id) {
                $this->assertEquals(48, $item['quantity']); // 50 - 2
            } else {
                $this->assertEquals(50, $item['quantity']); // Não vendidos
            }
        }
    }

    /**
     * Testa o fluxo de adição de estoque em lote
     *
     * @return void
     */
    public function test_bulk_inventory_addition_workflow(): void
    {
        // 1. Consultar estoque inicial
        $initialInventoryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        $initialInventoryData = $initialInventoryResponse->json('data');
        $initialQuantities = [];
        foreach ($initialInventoryData as $item) {
            $initialQuantities[$item['product_id']] = $item['quantity'];
        }

        // 2. Adicionar estoque em lote
        $products = Product::all();
        $bulkInventoryData = [
            [
                'product_id' => $products[0]->id,
                'quantity' => 10,
            ],
            [
                'product_id' => $products[1]->id,
                'quantity' => 15,
            ],
            [
                'product_id' => $products[2]->id,
                'quantity' => 5,
            ],
        ];

        $bulkResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/inventory', $bulkInventoryData);

        $bulkResponse->assertStatus(201)
                    ->assertJson(['success' => true]);

        // 3. Verificar se as quantidades foram somadas corretamente
        $updatedInventoryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        $updatedInventoryData = $updatedInventoryResponse->json('data');
        
        foreach ($updatedInventoryData as $item) {
            if ($item['product_id'] == $products[0]->id) {
                $this->assertEquals(60, $item['quantity']); // 50 + 10
            } elseif ($item['product_id'] == $products[1]->id) {
                $this->assertEquals(65, $item['quantity']); // 50 + 15
            } elseif ($item['product_id'] == $products[2]->id) {
                $this->assertEquals(55, $item['quantity']); // 50 + 5
            } else {
                $this->assertEquals(50, $item['quantity']); // Não alterados
            }
        }
    }

    /**
     * Testa validação de estoque insuficiente com dados reais
     *
     * @return void
     */
    public function test_insufficient_stock_validation_with_real_data(): void
    {
        // Tentar vender mais produtos do que disponível
        $products = Product::all();
        $saleData = [
            'items' => [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 60, // Mais que os 50 disponíveis
                ],
            ],
        ];

        $saleResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sales', $saleData);

        $saleResponse->assertStatus(422)
                    ->assertJson(['success' => false])
                    ->assertJsonStructure(['message']);

        // Verificar se o estoque não foi alterado
        $inventoryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/inventory');

        $inventoryData = $inventoryResponse->json('data');
        foreach ($inventoryData as $item) {
            $this->assertEquals(50, $item['quantity']); // Deve permanecer inalterado
        }
    }
}
