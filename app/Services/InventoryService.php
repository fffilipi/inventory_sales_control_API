<?php

namespace App\Services;

use App\Interfaces\InventoryServiceInterface;
use App\Respositories\InventoryRepository;

/**
 * Serviço responsável pela lógica de negócio do estoque
 * 
 * Este serviço implementa a camada de lógica de negócio para controle de estoque,
 * incluindo adição individual e em lote de produtos, e consulta consolidada.
 * Centraliza as regras de negócio relacionadas ao estoque.
 * 
 * @package App\Services
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryService implements InventoryServiceInterface
{
    /**
     * Repository de estoque injetado via dependência
     * 
     * @var InventoryRepository
     */
    protected $repository;

    /**
     * Construtor do serviço
     * 
     * @param InventoryRepository $repository Repository de estoque
     */
    public function __construct(InventoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Adiciona um produto ao estoque
     * 
     * Se o produto já existe no estoque, soma a quantidade.
     * Se não existe, cria um novo registro de estoque.
     * 
     * @param array $data Dados do estoque (product_id, quantity)
     * @return \App\Models\Inventory Registro de estoque criado/atualizado
     * @throws \Exception Quando ocorre erro na operação
     */
    public function addStock(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Adiciona múltiplos produtos ao estoque em lote
     * 
     * Processa uma lista de produtos para adição ao estoque,
     * aplicando a mesma lógica de soma para produtos existentes.
     * 
     * @param array $data Array de dados de estoque
     * @return array Lista de registros de estoque processados
     * @throws \Exception Quando ocorre erro na operação
     */
    public function addBulkStock(array $data)
    {
        $results = [];
        
        foreach ($data as $item) {
            $results[] = $this->repository->create($item);
        }
        
        return $results;
    }

    /**
     * Retorna a situação consolidada do estoque
     * 
     * Retorna uma visão agrupada do estoque com cálculos automáticos
     * de valores totais, lucro projetado e margem de lucro.
     * 
     * @return \Illuminate\Support\Collection Estoque consolidado
     */
    public function getStock()
    {
        return $this->repository->getAll();
    }
}
