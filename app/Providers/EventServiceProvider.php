<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SaleCompleted;
use App\Listeners\UpdateInventoryOnSale;

/**
 * Provider responsável pelo gerenciamento de eventos e listeners
 * 
 * Este provider configura o sistema de eventos da aplicação, definindo
 * quais listeners devem ser executados quando determinados eventos
 * são disparados. Centraliza o mapeamento entre eventos e suas
 * respectivas ações (listeners).
 * 
 * @package App\Providers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de eventos para seus respectivos listeners
     * 
     * Define quais listeners devem ser executados quando cada evento
     * é disparado. Cada evento pode ter múltiplos listeners que serão
     * executados em sequência.
     * 
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SaleCompleted::class => [
            UpdateInventoryOnSale::class,
        ],
    ];

    /**
     * Registra eventos customizados da aplicação
     * 
     * Este método é chamado durante a inicialização da aplicação
     * e é o local apropriado para registrar eventos customizados,
     * configurações de broadcasting e outras configurações
     * relacionadas ao sistema de eventos.
     * 
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determina se eventos e listeners devem ser descobertos automaticamente
     * 
     * Quando habilitado, o Laravel automaticamente descobre e registra
     * eventos e listeners baseado em convenções de nomenclatura.
     * 
     * @return bool True para descoberta automática, false para registro manual
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
