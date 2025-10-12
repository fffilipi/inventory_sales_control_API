<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\InventoryServiceInterface;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\InventoryRequest;

/**
 * Controller responsável por gerenciar o estoque de produtos
 * 
 * Este controller implementa as operações de controle de estoque,
 * incluindo adição de produtos ao estoque e consulta da situação atual.
 * Suporta adição individual ou em lote de produtos.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryController extends Controller
{
    /**
     * Serviço de estoque injetado via dependência
     * 
     * @var InventoryServiceInterface
     */
    protected $service;

    /**
     * Construtor do controller
     * 
     * @param InventoryServiceInterface $service Serviço de estoque
     */
    public function __construct(InventoryServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Adiciona produtos ao estoque
     * 
     * Este método permite adicionar produtos ao estoque de forma individual
     * ou em lote. Se o produto já existe no estoque, a quantidade é somada.
     * Calcula automaticamente valores totais e margem de lucro.
     * 
     * @param Request $request Requisição contendo dados do estoque
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException Quando os dados são inválidos
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/inventory (produto único)
     * {
     *     "product_id": 1,
     *     "quantity": 10
     * }
     * 
     * POST /api/inventory (múltiplos produtos)
     * [
     *     {"product_id": 1, "quantity": 5},
     *     {"product_id": 2, "quantity": 3}
     * ]
     */
    public function store(InventoryRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Verificar se é um array (múltiplos produtos) ou objeto único
            $input = $request->all();
            
            if (isset($input[0]) && is_array($input[0])) {
                // Múltiplos produtos - array de arrays
                $result = $this->service->addBulkStock($data);
                $message = count($data) . ' produtos adicionados ao estoque com sucesso';
            } else {
                // Produto único - objeto
                $result = $this->service->addStock($data);
                $message = 'Estoque adicionado com sucesso';
            }

            return ApiResponse::success($result, $message, 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    /**
     * Consulta a situação atual do estoque
     * 
     * Este método retorna uma visão consolidada do estoque atual,
     * agrupando produtos por ID e calculando automaticamente:
     * - Quantidade total por produto
     * - Valor total de custo
     * - Valor total de venda
     * - Lucro projetado
     * - Margem de lucro percentual
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * GET /api/inventory
     */
    public function index()
    {
        try {
            $inventory = $this->service->getStock();
            $message = count($inventory) > 0 ? 'Estoque consultado com sucesso' : 'Nenhum item encontrado no estoque';
            return ApiResponse::success($inventory, $message);
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro ao consultar estoque');
        }
    }
}
