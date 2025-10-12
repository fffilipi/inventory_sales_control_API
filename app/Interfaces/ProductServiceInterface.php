<?php

namespace App\Interfaces;

use App\DTOs\Product\ProductDTO;

/**
 * Interface para o serviço de produtos
 * 
 * Esta interface define os contratos para operações de lógica de negócio
 * relacionadas aos produtos do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface ProductServiceInterface
{
    /**
     * Cria um novo produto no sistema
     * 
     * @param ProductDTO $productDTO Dados do produto
     * @return ProductDTO Produto criado
     * @throws \Exception Quando ocorre erro na criação
     */
    public function createProduct(ProductDTO $productDTO): ProductDTO;

    /**
     * Retorna todos os produtos cadastrados
     * 
     * @return array Lista de produtos como DTOs
     */
    public function getAllProducts(): array;

    /**
     * Busca um produto específico por ID
     * 
     * @param int $id ID do produto
     * @return ProductDTO Produto encontrado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando produto não existe
     */
    public function getProduct(int $id): ProductDTO;
}
