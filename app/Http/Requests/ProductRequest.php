<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validação de produtos
 * 
 * Este Form Request centraliza as regras de validação para criação
 * e atualização de produtos, garantindo que os dados estejam corretos
 * antes de processar a operação.
 * 
 * @package App\Http\Requests
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductRequest extends FormRequest
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
            'sku' => 'required|string|max:255|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
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
            'sku.required' => 'O campo SKU é obrigatório.',
            'sku.string' => 'O campo SKU deve ser uma string.',
            'sku.max' => 'O campo SKU não pode ter mais de 255 caracteres.',
            'sku.unique' => 'Este SKU já está sendo usado.',
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'description.string' => 'O campo descrição deve ser uma string.',
            'cost_price.required' => 'O campo preço de custo é obrigatório.',
            'cost_price.numeric' => 'O campo preço de custo deve ser um número.',
            'cost_price.min' => 'O campo preço de custo deve ser maior ou igual a 0.',
            'sale_price.required' => 'O campo preço de venda é obrigatório.',
            'sale_price.numeric' => 'O campo preço de venda deve ser um número.',
            'sale_price.min' => 'O campo preço de venda deve ser maior ou igual a 0.',
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
            'sku' => 'SKU',
            'name' => 'nome',
            'description' => 'descrição',
            'cost_price' => 'preço de custo',
            'sale_price' => 'preço de venda',
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
            'Dados do produto inválidos'
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
