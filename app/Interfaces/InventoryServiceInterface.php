<?php

namespace App\Interfaces;

/**
 * Interface para o serviço de estoque
 * 
 * Esta interface define os contratos para operações de lógica de negócio
 * relacionadas ao controle de estoque do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface InventoryServiceInterface
{
    /**
     * Adiciona um produto ao estoque
     * 
     * @param array $data Dados do estoque
     * @return \App\Models\Inventory Registro de estoque criado/atualizado
     * @throws \Exception Quando ocorre erro na operação
     */
    public function addStock(array $data);

    /**
     * Adiciona múltiplos produtos ao estoque em lote
     * 
     * @param array $data Array de dados de estoque
     * @return array Lista de registros de estoque processados
     * @throws \Exception Quando ocorre erro na operação
     */
    public function addBulkStock(array $data);

    /**
     * Retorna a situação consolidada do estoque
     * 
     * @return \Illuminate\Support\Collection Estoque consolidado
     */
    public function getStock();
}
