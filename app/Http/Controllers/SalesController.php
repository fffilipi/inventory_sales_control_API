<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\SalesServiceInterface;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\SaleRequest;

/**
 * Controller responsável por gerenciar vendas
 * 
 * Este controller implementa as operações de controle de vendas,
 * incluindo registro de novas vendas e consulta de detalhes.
 * Valida disponibilidade de estoque antes de processar vendas.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SalesController extends Controller
{
    /**
     * Serviço de vendas injetado via dependência
     * 
     * @var SalesServiceInterface
     */
    protected $service;

    /**
     * Construtor do controller
     * 
     * @param SalesServiceInterface $service Serviço de vendas
     */
    public function __construct(SalesServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Registra uma nova venda no sistema
     * 
     * Este método processa uma nova venda com múltiplos itens,
     * validando a disponibilidade de estoque antes de processar.
     * Calcula automaticamente valores totais, custos e margem de lucro.
     * Atualiza o estoque automaticamente após a venda.
     * 
     * @param Request $request Requisição contendo os itens da venda
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException Quando os dados são inválidos
     * @throws \Exception Quando há estoque insuficiente ou erro interno
     * 
     * @example
     * POST /api/sales
     * {
     *     "items": [
     *         {
     *             "product_id": 1,
     *             "quantity": 2
     *         },
     *         {
     *             "product_id": 2,
     *             "quantity": 1
     *         }
     *     ]
     * }
     */
    public function store(SaleRequest $request)
    {
        try {
            $data = $request->validated();

            $sale = $this->service->createSale($data);
            return ApiResponse::success($sale, 'Venda registrada com sucesso', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    /**
     * Consulta os detalhes de uma venda específica
     * 
     * Este método retorna informações completas de uma venda,
     * incluindo todos os itens vendidos, valores calculados,
     * custos e margem de lucro da transação.
     * 
     * @param int $id ID da venda a ser consultada
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando a venda não existe
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * GET /api/sales/1
     */
    public function show($id)
    {
        try {
            $sale = $this->service->getSaleDetails($id);
            return ApiResponse::success($sale, 'Venda encontrada com sucesso');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound('Venda não encontrada');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro ao buscar venda');
        }
    }
}
