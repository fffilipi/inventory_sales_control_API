<?php

namespace App\Interfaces;

/**
 * Interface para o serviço de vendas
 * 
 * Esta interface define os contratos para operações de lógica de negócio
 * relacionadas ao processamento de vendas do sistema.
 * 
 * @package App\Interfaces
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
interface SalesServiceInterface
{
    /**
     * Processa uma nova venda no sistema
     * 
     * @param array $data Dados da venda
     * @return \App\Models\Sale Venda processada
     * @throws \Exception Quando há estoque insuficiente ou erro no processamento
     */
    public function createSale(array $data);

    /**
     * Busca os detalhes de uma venda específica
     * 
     * @param int $id ID da venda
     * @return \App\Models\Sale Venda com itens relacionados
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando venda não existe
     */
    public function getSaleDetails(int $id);
}
