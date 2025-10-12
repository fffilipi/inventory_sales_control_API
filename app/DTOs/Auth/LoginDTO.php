<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

/**
 * DTO para dados de login
 * 
 * Este DTO encapsula os dados necessários para realizar
 * o login de um usuário no sistema.
 * 
 * @package App\DTOs\Auth
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class LoginDTO extends BaseDTO
{
    /**
     * Email do usuário
     * 
     * @var string
     */
    public string $email;

    /**
     * Senha do usuário
     * 
     * @var string
     */
    public string $password;

    /**
     * Construtor do DTO de login
     * 
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
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
            $data['email'],
            $data['password']
        );
    }

    /**
     * Retorna os dados como array para autenticação
     * 
     * @return array
     */
    public function toCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
