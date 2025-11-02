<?php

namespace App\Interfaces;

/**
 * Interface para o repository de vendas
 * 
 * Esta interface define os contratos para operações de acesso a dados
 * relacionadas às vendas do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface SalesRepositoryInterface
{
    /**
     * Cria uma nova venda no banco de dados
     * 
     * @param array $data Dados da venda
     * @return \App\Models\Sale Venda criada
     * @throws \Exception Quando ocorre erro na criação
     */
    public function create(array $data);

    /**
     * Busca uma venda específica por ID com itens relacionados
     * 
     * @param int $id ID da venda
     * @return \App\Models\Sale Venda com itens e produtos relacionados
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando venda não existe
     */
    public function find(int $id);

    /**
     * Atualiza uma venda existente
     * 
     * @param int $id ID da venda
     * @param array $data Dados para atualização
     * @return \App\Models\Sale Venda atualizada
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando venda não existe
     */
    public function update(int $id, array $data);

    /**
     * Cria um novo item de venda
     * 
     * @param array $data Dados do item de venda
     * @return \App\Models\SaleItem Item de venda criado
     * @throws \Exception Quando ocorre erro na criação
     */
    public function createItem(array $data);
}
