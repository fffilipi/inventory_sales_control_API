<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/sales",
 *     tags={"Vendas"},
 *     summary="Criar venda",
 *     description="Processa uma nova venda com múltiplos itens, validando estoque e calculando valores automaticamente",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/SaleRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Venda registrada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Venda registrada com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/Sale"),
 *             @OA\Property(property="timestamp", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token de acesso inválido ou expirado",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Dados de venda inválidos ou estoque insuficiente",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/sales/{id}",
 *     tags={"Vendas"},
 *     summary="Consultar venda",
 *     description="Retorna os detalhes completos de uma venda específica",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID da venda",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Venda encontrada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Venda encontrada com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/Sale"),
 *             @OA\Property(property="timestamp", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token de acesso inválido ou expirado",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Venda não encontrada",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     )
 * )
 */
class SalesControllerSwagger
{
    //
}
