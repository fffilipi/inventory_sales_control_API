<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ApiResponse;

/**
 * Middleware responsável por autenticar requisições da API
 * 
 * Este middleware verifica se a requisição possui um token válido
 * e se o usuário está autenticado via Laravel Sanctum.
 * 
 * @package App\Http\Middleware
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class AuthenticateApi
{
    /**
     * Processa uma requisição HTTP para verificar autenticação
     * 
     * Verifica se a requisição possui um token Bearer válido e se
     * o usuário está autenticado. Se não estiver, retorna erro 401.
     * 
     * @param Request $request Requisição HTTP
     * @param Closure $next Próximo middleware na cadeia
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o token está presente
        if (!$request->bearerToken()) {
            return ApiResponse::error('Token de acesso não fornecido', 401);
        }

        // Tenta autenticar o usuário com o token
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return ApiResponse::error('Token de acesso inválido ou expirado', 401);
        }

        // Define o usuário autenticado na request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
