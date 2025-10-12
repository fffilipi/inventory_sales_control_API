<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\TokenResponseDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Serviço responsável pela lógica de negócio de autenticação
 * 
 * Este serviço implementa a camada de lógica de negócio para autenticação,
 * incluindo login, registro e gerenciamento de tokens.
 * Utiliza DTOs para transferência de dados entre camadas.
 * 
 * @package App\Services
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class AuthService
{
    /**
     * Realiza o login do usuário
     * 
     * @param LoginDTO $loginDTO Dados de login
     * @return TokenResponseDTO|null Resposta com token ou null se falhar
     */
    public function login(LoginDTO $loginDTO): ?TokenResponseDTO
    {
        $credentials = $loginDTO->toCredentials();
        
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        $expiresAt = now()->addHours(24)->toISOString();

        return TokenResponseDTO::fromUserAndToken($user, $token, $expiresAt);
    }

    /**
     * Registra um novo usuário
     * 
     * @param RegisterDTO $registerDTO Dados de registro
     * @return TokenResponseDTO Resposta com token
     */
    public function register(RegisterDTO $registerDTO): TokenResponseDTO
    {
        $userData = $registerDTO->toUserData();
        $userData['password'] = Hash::make($userData['password']);

        $user = User::create($userData);
        $token = $user->createToken('api-token')->plainTextToken;
        $expiresAt = now()->addHours(24)->toISOString();

        return TokenResponseDTO::fromUserAndToken($user, $token, $expiresAt);
    }

    /**
     * Realiza o logout do usuário
     * 
     * @param User $user Usuário a ser deslogado
     * @return bool Sucesso da operação
     */
    public function logout(User $user): bool
    {
        try {
            $user->currentAccessToken()->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revoga todos os tokens do usuário
     * 
     * @param User $user Usuário
     * @return bool Sucesso da operação
     */
    public function revokeAllTokens(User $user): bool
    {
        try {
            $user->tokens()->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retorna os dados do usuário autenticado
     * 
     * @param User $user Usuário autenticado
     * @return array Dados do usuário
     */
    public function getUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
        ];
    }
}
