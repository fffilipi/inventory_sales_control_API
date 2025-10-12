<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Classe responsável por padronizar as respostas da API
 * 
 * Esta classe fornece métodos estáticos para gerar respostas JSON
 * padronizadas para diferentes tipos de operações da API, garantindo
 * consistência em todas as respostas.
 * 
 * @package App\Http\Responses
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ApiResponse
{
    /**
     * Retorna uma resposta de sucesso padronizada
     * 
     * @param mixed $data Dados a serem retornados na resposta
     * @param string $message Mensagem de sucesso
     * @param int $statusCode Código de status HTTP (padrão: 200)
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Operação realizada com sucesso', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Retorna uma resposta de erro genérica
     * 
     * @param string $message Mensagem de erro
     * @param int $statusCode Código de status HTTP (padrão: 400)
     * @param mixed $errors Detalhes adicionais do erro
     * @return JsonResponse
     */
    public static function error(string $message, int $statusCode = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Retorna uma resposta de erro de validação
     * 
     * @param array $errors Array com os erros de validação
     * @param string $message Mensagem de erro (padrão: 'Dados inválidos')
     * @return JsonResponse
     */
    public static function validationError($errors, string $message = 'Dados inválidos'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ], 422);
    }

    /**
     * Retorna uma resposta de recurso não encontrado (404)
     * 
     * @param string $message Mensagem de erro (padrão: 'Recurso não encontrado')
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'timestamp' => now()->toISOString(),
        ], 404);
    }

    /**
     * Retorna uma resposta de erro interno do servidor (500)
     * 
     * @param string $message Mensagem de erro (padrão: 'Erro interno do servidor')
     * @return JsonResponse
     */
    public static function serverError(string $message = 'Erro interno do servidor'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ], 500);
    }
}
