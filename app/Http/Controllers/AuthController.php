<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Services\AuthService;
use OpenApi\Annotations as OA;

/**
 * Controller responsável por gerenciar a autenticação de usuários
 * 
 * Este controller implementa todas as funcionalidades de autenticação
 * da API, incluindo login, registro, logout e gerenciamento de tokens.
 * Utiliza Laravel Sanctum para autenticação baseada em tokens.
 * 
 * @package App\Http\Controllers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class AuthController extends Controller
{
    /**
     * Serviço de autenticação injetado via dependência
     *
     * @var AuthService
     */
    protected $authService;

    /**
     * Construtor do controller
     *
     * @param AuthService $authService Serviço de autenticação
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Realiza o login do usuário e gera um token de acesso
     * 
     * Este método autentica o usuário com email e senha, e retorna
     * um token JWT válido por 24 horas para acesso às rotas protegidas.
     * 
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Autenticação"},
     *     summary="Realizar login",
     *     description="Autentica o usuário e retorna um token de acesso JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login realizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/TokenResponse"),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados de login inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/ApiError")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(ref="#/components/schemas/ApiError")
     *     )
     * )
     * 
     * @param LoginRequest $request Requisição contendo email e password
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws ValidationException Quando os dados de login são inválidos
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/auth/login
     * {
     *     "email": "admin@inventory.com",
     *     "password": "password123"
     * }
     */
    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();
            $loginDTO = LoginDTO::fromArray($data);

            $tokenResponse = $this->authService->login($loginDTO);
            
            if (!$tokenResponse) {
                return ApiResponse::error('Credenciais inválidas', 401);
            }

            return ApiResponse::success($tokenResponse->toArray(), 'Login realizado com sucesso');

        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro interno do servidor');
        }
    }

    /**
     * Registra um novo usuário no sistema
     * 
     * Este método cria um novo usuário com os dados fornecidos,
     * criptografa a senha e retorna um token de acesso imediatamente.
     * 
     * @param Request $request Requisição contendo name, email, password e password_confirmation
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws ValidationException Quando os dados de registro são inválidos
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/auth/register
     * {
     *     "name": "João Silva",
     *     "email": "joao@exemplo.com",
     *     "password": "senha123",
     *     "password_confirmation": "senha123"
     * }
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $registerDTO = RegisterDTO::fromArray($data);

            $tokenResponse = $this->authService->register($registerDTO);

            return ApiResponse::success($tokenResponse->toArray(), 'Usuário registrado com sucesso', 201);

        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro interno do servidor');
        }
    }

    /**
     * Realiza o logout do usuário revogando o token atual
     * 
     * Este método revoga o token de acesso atual do usuário autenticado,
     * invalidando-o imediatamente e impedindo futuras utilizações.
     * 
     * @param Request $request Requisição contendo o token de autenticação
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/auth/logout
     * Headers: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }

            $success = $this->authService->logout($user);

            if (!$success) {
                return ApiResponse::serverError('Erro ao realizar logout');
            }

            return ApiResponse::success(null, 'Logout realizado com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro interno do servidor: ' . $e->getMessage());
        }
    }

    /**
     * Retorna as informações do usuário autenticado
     * 
     * Este método retorna os dados do usuário atualmente autenticado,
     * incluindo ID, nome, email e data de criação da conta.
     * 
     * @param Request $request Requisição contendo o token de autenticação
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * GET /api/auth/me
     * Headers: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }

            $userData = $this->authService->getUserData($user);

            return ApiResponse::success([
                'user' => $userData,
            ], 'Informações do usuário obtidas com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro interno do servidor: ' . $e->getMessage());
        }
    }

    /**
     * Revoga todos os tokens de acesso do usuário autenticado
     * 
     * Este método revoga todos os tokens de acesso ativos do usuário,
     * forçando o logout em todos os dispositivos onde o usuário está logado.
     * 
     * @param Request $request Requisição contendo o token de autenticação
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Exception Quando ocorre erro interno do servidor
     * 
     * @example
     * POST /api/auth/revoke-all-tokens
     * Headers: Authorization: Bearer {token}
     */
    public function revokeAllTokens(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::error('Usuário não autenticado', 401);
            }

            $success = $this->authService->revokeAllTokens($user);

            if (!$success) {
                return ApiResponse::serverError('Erro ao revogar tokens');
            }

            return ApiResponse::success(null, 'Todos os tokens foram revogados com sucesso');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Erro interno do servidor: ' . $e->getMessage());
        }
    }
}
