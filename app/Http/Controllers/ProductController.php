<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ProductServiceInterface;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\ProductRequest;
use App\DTOs\Product\ProductDTO;

/**
 * Controller responsável por gerenciar produtos
 * 
 * Este controller implementa as operações CRUD para produtos,
 * incluindo criação e listagem de produtos do sistema.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductController extends Controller
{
    /**
     * Serviço de produtos injetado via dependência
     * 
     * @var ProductServiceInterface
     */
    protected $service;

    /**
     * Construtor do controller
     * 
     * @param ProductServiceInterface $service Serviço de produtos
     */
    public function __construct(ProductServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Cria um novo produto no sistema
     * 
     * Este método valida os dados fornecidos e cria um novo produto
     * com SKU único, nome, descrição e preços de custo e venda.
     * 
     * @param Request $request Requisição contendo os dados do produto
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException Quando os dados são inválidos
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/products
     * {
     *     "sku": "PROD001",
     *     "name": "Produto Exemplo",
     *     "description": "Descrição do produto",
     *     "cost_price": 10.50,
     *     "sale_price": 15.00
     * }
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $productDTO = ProductDTO::fromArray($data);

            $product = $this->service->createProduct($productDTO);
            return ApiResponse::success($product->toArray(), 'Produto criado com sucesso', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    /**
     * Lista todos os produtos cadastrados no sistema
     * 
     * Este método retorna uma lista completa de todos os produtos
     * cadastrados, incluindo suas informações básicas como SKU,
     * nome, descrição e preços.
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * GET /api/products
     */
    public function index()
    {
        try {
            $products = $this->service->getAllProducts();
            $productsArray = array_map(function ($productDTO) {
                return $productDTO->toArray();
            }, $products);
            
            $message = count($productsArray) > 0 ? 'Produtos consultados com sucesso' : 'Nenhum produto encontrado';
            return ApiResponse::success($productsArray, $message);
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro ao consultar produtos');
        }
    }
}
