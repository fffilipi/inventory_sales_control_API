<?php

namespace App\Http\Controllers\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/products",
 *     tags={"Produtos"},
 *     summary="Listar produtos",
 *     description="Retorna uma lista de todos os produtos cadastrados no sistema",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de produtos retornada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Produtos consultados com sucesso"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Product")
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
 *     path="/api/products",
 *     tags={"Produtos"},
 *     summary="Criar produto",
 *     description="Cria um novo produto no sistema com SKU único",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Produto criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Produto criado com sucesso"),
 *             @OA\Property(property="data", ref="#/components/schemas/Product"),
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
 *         description="Dados do produto inválidos",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor",
 *         @OA\JsonContent(ref="#/components/schemas/ApiError")
 *     )
 * )
 */
class ProductControllerSwagger
{
    //
}
