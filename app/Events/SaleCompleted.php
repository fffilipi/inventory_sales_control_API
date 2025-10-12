<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;

/**
 * Evento disparado quando uma venda é finalizada
 * 
 * Este evento é disparado automaticamente quando uma venda é completada
 * com sucesso, permitindo que listeners executem ações relacionadas,
 * como atualização de estoque, envio de notificações, etc.
 * 
 * @package App\Events
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class SaleCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Modelo da venda que foi finalizada
     * 
     * @var \App\Models\Sale
     */
    public $sale;

    /**
     * Cria uma nova instância do evento
     * 
     * @param \App\Models\Sale $sale Venda que foi finalizada
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }
}
