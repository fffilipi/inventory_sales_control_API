<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/inventory",
 *     tags={"Estoque"},
 *     summary="Consultar estoque",
 *     description="Retorna o estoque consolidado com cálculos dinâmicos de valores e margem de lucro",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Estoque consultado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Estoque consultado com sucesso"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/InventoryItem")
 *             ),
 *             @OA\Property(property="timestamp", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Token de acesso inválido ou expirado",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/inventory",
 *     tags={"Estoque"},
 *     summary="Adicionar estoque",
 *     description="Adiciona produtos ao estoque. Suporta adição individual ou em lote. Se o produto já existe, soma a quantidade.",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dados do estoque (objeto único ou array para lote)",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/InventoryRequest"),
 *                 @OA\Schema(ref="#/components/schemas/BulkInventoryRequest")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Estoque adicionado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Estoque adicionado com sucesso"),
 *             @OA\Property(property="data", type="object"),
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
 *         description="Dados de estoque inválidos",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     )
 * )
 */
class InventoryControllerSwagger
{
    //
}
