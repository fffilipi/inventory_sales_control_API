<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validação de vendas
 * 
 * Este Form Request centraliza as regras de validação para criação
 * de vendas, garantindo que os dados dos itens estejam corretos
 * antes de processar a venda.
 * 
 * @package App\Http\Requests
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
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
            'items.required' => 'O campo itens é obrigatório.',
            'items.array' => 'O campo itens deve ser um array.',
            'items.min' => 'Deve haver pelo menos 1 item na venda.',
            'items.*.product_id.required' => 'O campo ID do produto é obrigatório.',
            'items.*.product_id.integer' => 'O campo ID do produto deve ser um número inteiro.',
            'items.*.product_id.exists' => 'O produto selecionado não existe.',
            'items.*.quantity.required' => 'O campo quantidade é obrigatório.',
            'items.*.quantity.integer' => 'O campo quantidade deve ser um número inteiro.',
            'items.*.quantity.min' => 'O campo quantidade deve ser pelo menos 1.',
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
            'items' => 'itens',
            'items.*.product_id' => 'ID do produto',
            'items.*.quantity' => 'quantidade',
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
            'Dados de venda inválidos'
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
