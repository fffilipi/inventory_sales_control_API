<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

/**
 * DTO para resposta de token de autenticação
 * 
 * Este DTO encapsula os dados retornados após login/registro
 * bem-sucedido, incluindo informações do usuário e token.
 * 
 * @package App\DTOs\Auth
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class TokenResponseDTO extends BaseDTO
{
    /**
     * Dados do usuário
     * 
     * @var array
     */
    public array $user;

    /**
     * Token de acesso
     * 
     * @var string
     */
    public string $token;

    /**
     * Tipo do token
     * 
     * @var string
     */
    public string $token_type;

    /**
     * Data de expiração do token
     * 
     * @var string
     */
    public string $expires_at;

    /**
     * Construtor do DTO de resposta de token
     * 
     * @param array $user Dados do usuário
     * @param string $token Token de acesso
     * @param string $token_type Tipo do token
     * @param string $expires_at Data de expiração
     */
    public function __construct(
        array $user,
        string $token,
        string $token_type = 'Bearer',
        string $expires_at = ''
    ) {
        $this->user = $user;
        $this->token = $token;
        $this->token_type = $token_type;
        $this->expires_at = $expires_at;
    }

    /**
     * Cria uma instância do DTO a partir de dados do usuário e token
     * 
     * @param \App\Models\User $user Usuário autenticado
     * @param string $token Token de acesso
     * @param string $expires_at Data de expiração
     * @return static
     */
    public static function fromUserAndToken($user, string $token, string $expires_at = ''): static
    {
        return new static(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            $token,
            'Bearer',
            $expires_at
        );
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data Dados para criar o DTO
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            $data['user'],
            $data['token'],
            $data['token_type'] ?? 'Bearer',
            $data['expires_at'] ?? ''
        );
    }
}
