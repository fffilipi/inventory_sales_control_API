<?php

namespace App\Interfaces;

/**
 * Interface para o repository de produtos
 * 
 * Esta interface define os contratos para operações de acesso a dados
 * relacionadas aos produtos do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface ProductRepositoryInterface
{
    /**
     * Cria um novo produto no banco de dados
     * 
     * @param array $data Dados do produto
     * @return \App\Models\Product Produto criado
     * @throws \Exception Quando ocorre erro na criação
     */
    public function create(array $data);

    /**
     * Retorna todos os produtos cadastrados
     * 
     * @return \Illuminate\Database\Eloquent\Collection Lista de produtos
     */
    public function getAll();

    /**
     * Busca um produto específico por ID
     * 
     * @param int $id ID do produto
     * @return \App\Models\Product Produto encontrado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando produto não existe
     */
    public function find(int $id);
}
