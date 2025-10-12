<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API de Controle de Estoque e Vendas",
 *     version="1.0.0",
 *     description="API RESTful para gerenciamento de produtos, estoque e vendas com autenticação via Laravel Sanctum",
 *     @OA\Contact(
 *         email="admin@inventory.com",
 *         name="Sistema de Controle de Estoque e Vendas"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de autenticação JWT via Laravel Sanctum"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints para autenticação de usuários"
 * )
 * 
 * @OA\Tag(
 *     name="Produtos",
 *     description="Endpoints para gerenciamento de produtos"
 * )
 * 
 * @OA\Tag(
 *     name="Estoque",
 *     description="Endpoints para controle de estoque"
 * )
 * 
 * @OA\Tag(
 *     name="Vendas",
 *     description="Endpoints para processamento de vendas"
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso"),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiError",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Erro na operação"),
 *     @OA\Property(property="errors", type="object"),
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="sku", type="string", example="PROD001"),
 *     @OA\Property(property="name", type="string", example="Smartphone Samsung Galaxy S24"),
 *     @OA\Property(property="description", type="string", example="Smartphone Samsung Galaxy S24 com 128GB de armazenamento"),
 *     @OA\Property(property="cost_price", type="number", format="float", example=800.00),
 *     @OA\Property(property="sale_price", type="number", format="float", example=1200.00),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="InventoryItem",
 *     type="object",
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="quantity", type="integer", example=50),
 *     @OA\Property(property="total_cost_value", type="number", format="float", example=40000.00),
 *     @OA\Property(property="total_sale_value", type="number", format="float", example=60000.00),
 *     @OA\Property(property="projected_profit", type="number", format="float", example=20000.00),
 *     @OA\Property(property="profit_margin_percentage", type="number", format="float", example=33.33),
 *     @OA\Property(property="last_updated", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="SaleItem",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="sale_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="unit_price", type="number", format="float", example=1200.00),
 *     @OA\Property(property="unit_cost", type="number", format="float", example=800.00),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Sale",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="total_amount", type="number", format="float", example=2400.00),
 *     @OA\Property(property="total_cost", type="number", format="float", example=1600.00),
 *     @OA\Property(property="total_profit", type="number", format="float", example=800.00),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/SaleItem")),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Administrador"),
 *     @OA\Property(property="email", type="string", format="email", example="admin@inventory.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-12T10:30:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="admin@inventory.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="João Silva"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com"),
 *     @OA\Property(property="password", type="string", format="password", example="senha123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="senha123")
 * )
 * 
 * @OA\Schema(
 *     schema="ProductRequest",
 *     type="object",
 *     required={"sku", "name", "cost_price", "sale_price"},
 *     @OA\Property(property="sku", type="string", example="PROD001"),
 *     @OA\Property(property="name", type="string", example="Produto Exemplo"),
 *     @OA\Property(property="description", type="string", example="Descrição do produto"),
 *     @OA\Property(property="cost_price", type="number", format="float", example=100.00),
 *     @OA\Property(property="sale_price", type="number", format="float", example=150.00)
 * )
 * 
 * @OA\Schema(
 *     schema="InventoryRequest",
 *     type="object",
 *     required={"product_id", "quantity"},
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=10)
 * )
 * 
 * @OA\Schema(
 *     schema="BulkInventoryRequest",
 *     type="array",
 *     @OA\Items(
 *         type="object",
 *         required={"product_id", "quantity"},
 *         @OA\Property(property="product_id", type="integer", example=1),
 *         @OA\Property(property="quantity", type="integer", example=10)
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SaleRequest",
 *     type="object",
 *     required={"items"},
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id", "quantity"},
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="TokenResponse",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Administrador"),
 *     @OA\Property(property="email", type="string", format="email", example="admin@inventory.com"),
 *     @OA\Property(property="token", type="string", example="1|abcdef123456..."),
 *     @OA\Property(property="token_type", type="string", example="Bearer"),
 *     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-01-13T10:30:00.000000Z")
 * )
 */
abstract class Controller
{
    //
}