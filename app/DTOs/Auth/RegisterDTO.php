<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

/**
 * DTO para dados de registro de usuário
 * 
 * Este DTO encapsula os dados necessários para registrar
 * um novo usuário no sistema.
 * 
 * @package App\DTOs\Auth
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class RegisterDTO extends BaseDTO
{
    /**
     * Nome do usuário
     * 
     * @var string
     */
    public string $name;

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
     * Confirmação da senha
     * 
     * @var string
     */
    public string $password_confirmation;

    /**
     * Construtor do DTO de registro
     * 
     * @param string $name Nome do usuário
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @param string $password_confirmation Confirmação da senha
     */
    public function __construct(
        string $name,
        string $email,
        string $password,
        string $password_confirmation
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->password_confirmation = $password_confirmation;
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
            $data['name'],
            $data['email'],
            $data['password'],
            $data['password_confirmation']
        );
    }

    /**
     * Retorna os dados para criação do usuário (sem confirmação de senha)
     * 
     * @return array
     */
    public function toUserData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
