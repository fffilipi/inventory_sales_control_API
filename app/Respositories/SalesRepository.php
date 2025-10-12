<?php

namespace App\Respositories;

use App\Interfaces\SalesRepositoryInterface;
use App\Models\Sale;

/**
 * Repository responsável pelo acesso aos dados de vendas
 * 
 * Este repository implementa a camada de acesso a dados para vendas,
 * fornecendo métodos para operações CRUD e consultas relacionadas.
 * 
 * @package App\Respositories
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SalesRepository implements SalesRepositoryInterface
{
    /**
     * Modelo de venda injetado via dependência
     * 
     * @var Sale
     */
    protected $model;

    /**
     * Construtor do repository
     * 
     * @param Sale $sale Modelo de venda
     */
    public function __construct(Sale $sale)
    {
        $this->model = $sale;
    }

    /**
     * Cria uma nova venda no banco de dados
     * 
     * @param array $data Dados da venda
     * @return \App\Models\Sale Venda criada
     * @throws \Exception Quando ocorre erro na criação
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Busca uma venda específica por ID com itens relacionados
     * 
     * @param int $id ID da venda
     * @return \App\Models\Sale Venda com itens e produtos relacionados
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando venda não existe
     */
    public function find(int $id)
    {
        return $this->model->with('items.product')->findOrFail($id);
    }
}
