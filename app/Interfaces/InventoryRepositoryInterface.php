<?php

namespace App\Interfaces;

/**
 * Interface para o repository de estoque
 * 
 * Esta interface define os contratos para operações de acesso a dados
 * relacionadas ao controle de estoque do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface InventoryRepositoryInterface
{
    /**
     * Cria ou atualiza um registro de estoque
     * 
     * @param array $data Dados do estoque
     * @return \App\Models\Inventory Registro de estoque criado/atualizado
     * @throws \Exception Quando ocorre erro na operação
     */
    public function create(array $data);

    /**
     * Retorna o estoque consolidado com cálculos dinâmicos
     * 
     * @return \Illuminate\Support\Collection Estoque consolidado
     */
    public function getAll();

    /**
     * Busca um registro de estoque específico por ID
     * 
     * @param int $id ID do registro de estoque
     * @return \App\Models\Inventory|null Registro de estoque encontrado ou null
     */
    public function find(int $id);

    /**
     * Busca um registro de estoque por ID do produto
     * 
     * @param int $productId ID do produto
     * @return \App\Models\Inventory|null Registro de estoque encontrado ou null
     */
    public function findByProductId(int $productId);
}
