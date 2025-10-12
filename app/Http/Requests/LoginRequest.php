<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validação de login
 * 
 * Este Form Request centraliza as regras de validação para o processo
 * de login de usuários, garantindo que os dados estejam corretos
 * antes de processar a autenticação.
 * 
 * @package App\Http\Requests
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Retorna as mensagens de erro personalizadas para as regras de validação
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser uma string.',
            'password.min' => 'O campo senha deve ter pelo menos 6 caracteres.',
        ];
    }

    /**
     * Retorna os atributos personalizados para as mensagens de erro
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'senha',
        ];
    }

    /**
     * Trata uma tentativa de validação falha
     * 
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = \App\Http\Responses\ApiResponse::validationError(
            $validator->errors()->toArray(),
            'Dados de login inválidos'
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
