<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validação de estoque
 * 
 * Este Form Request centraliza as regras de validação para adição
 * de produtos ao estoque, suportando tanto adição individual quanto em lote.
 * 
 * @package App\Http\Requests
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryRequest extends FormRequest
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
        $input = $this->all();
        
        // Verifica se é um array (múltiplos produtos) ou objeto único
        if (isset($input[0]) && is_array($input[0])) {
            // Múltiplos produtos - array de arrays
            return [
                '*.product_id' => 'required|integer|exists:products,id',
                '*.quantity' => 'required|integer|min:1',
            ];
        } else {
            // Produto único - objeto
            return [
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ];
        }
    }

    /**
     * Retorna as mensagens de erro personalizadas para as regras de validação
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'O campo ID do produto é obrigatório.',
            'product_id.integer' => 'O campo ID do produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'quantity.required' => 'O campo quantidade é obrigatório.',
            'quantity.integer' => 'O campo quantidade deve ser um número inteiro.',
            'quantity.min' => 'O campo quantidade deve ser pelo menos 1.',
            '*.product_id.required' => 'O campo ID do produto é obrigatório.',
            '*.product_id.integer' => 'O campo ID do produto deve ser um número inteiro.',
            '*.product_id.exists' => 'O produto selecionado não existe.',
            '*.quantity.required' => 'O campo quantidade é obrigatório.',
            '*.quantity.integer' => 'O campo quantidade deve ser um número inteiro.',
            '*.quantity.min' => 'O campo quantidade deve ser pelo menos 1.',
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
            'product_id' => 'ID do produto',
            'quantity' => 'quantidade',
            '*.product_id' => 'ID do produto',
            '*.quantity' => 'quantidade',
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
            'Dados de estoque inválidos'
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
