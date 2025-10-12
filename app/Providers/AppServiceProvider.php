<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Provider principal da aplicação
 * 
 * Este provider é responsável por registrar e inicializar serviços
 * globais da aplicação. É o local central para configurações de
 * serviços, bindings de dependências e inicializações que devem
 * estar disponíveis em toda a aplicação.
 * 
 * @package App\Providers
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços da aplicação no container de injeção de dependência
     * 
     * Este método é chamado durante o processo de inicialização da aplicação
     * e é o local apropriado para registrar bindings de serviços, singletons
     * e outras configurações de dependências.
     * 
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa serviços da aplicação após o registro
     * 
     * Este método é chamado após todos os serviços terem sido registrados
     * e é o local apropriado para realizar inicializações, configurações
     * de middleware, validações customizadas e outras configurações
     * que dependem de serviços já registrados.
     * 
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
