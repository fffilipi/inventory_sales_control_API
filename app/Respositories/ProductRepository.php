<?php

namespace App\Respositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

/**
 * Repository responsável pelo acesso aos dados de produtos
 * 
 * Este repository implementa a camada de acesso a dados para produtos,
 * fornecendo métodos para operações CRUD básicas no banco de dados.
 * 
 * @package App\Respositories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Cria um novo produto no banco de dados
     * 
     * @param array $data Dados do produto
     * @return \App\Models\Product Produto criado
     * @throws \Exception Quando ocorre erro na criação
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * Retorna todos os produtos cadastrados
     * 
     * @return \Illuminate\Database\Eloquent\Collection Lista de produtos
     */
    public function getAll()
    {
        return Product::all();
    }

    /**
     * Busca um produto específico por ID
     * 
     * @param int $id ID do produto
     * @return \App\Models\Product Produto encontrado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando produto não existe
     */
    public function find(int $id)
    {
        return Product::findOrFail($id);
    }
}
