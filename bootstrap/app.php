<?php

/**
 * Arquivo de configuração principal da aplicação Laravel
 * 
 * Este arquivo é responsável por configurar e inicializar todos os aspectos
 * da aplicação, incluindo rotas, middleware, providers, tratamento de exceções
 * e injeção de dependências. É o ponto de entrada para todas as configurações
 * do sistema de controle de estoque e vendas.
 * 
 * @package Bootstrap
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Configuração e inicialização da aplicação Laravel
 * 
 * Cria a instância da aplicação com todas as configurações necessárias
 * para o funcionamento do sistema de controle de estoque e vendas.
 */
$app = Application::configure(basePath: dirname(__DIR__))

    /**
     * Configuração de rotas da aplicação
     * 
     * Define as rotas web, comandos de console e endpoint de health check
     */
    ->withRouting(
        web: __DIR__.'/../routes/web.php',        // Rotas web da aplicação
        commands: __DIR__.'/../routes/console.php', // Comandos de console
        health: '/up',                            // Endpoint de health check
    )

    /**
     * Configuração de middleware global
     * 
     * Define middlewares que serão aplicados globalmente na aplicação,
     * incluindo autenticação da API e middleware do Sanctum.
     */
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias para middlewares customizados
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
        ]);
        
        // Middleware global do Sanctum para autenticação de API
        $middleware->use([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })

    /**
     * Registro de service providers
     * 
     * Registra os providers customizados da aplicação que serão
     * carregados durante a inicialização.
     */
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])

    /**
     * Configuração de tratamento de exceções
     * 
     * Define como exceções serão tratadas globalmente na aplicação.
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tratamento global de exceções pode ser adicionado aqui
    })
    
    ->create();

/**
 * ========================================
 * ROTAS DE AUTENTICAÇÃO (PÚBLICAS)
 * ========================================
 * 
 * Rotas que não requerem autenticação para acesso.
 * Permitem login e registro de novos usuários.
 */
$app->router->group(['prefix' => 'api/auth'], function ($router) {
    $router->post('/login', 'App\Http\Controllers\AuthController@login');
    $router->post('/register', 'App\Http\Controllers\AuthController@register');
});

/**
 * ========================================
 * ROTAS DA API (PROTEGIDAS)
 * ========================================
 * 
 * Todas as rotas abaixo requerem autenticação via token.
 * Middleware 'auth.api' é aplicado automaticamente.
 */
$app->router->group(['prefix' => 'api', 'middleware' => 'auth.api'], function ($router) {
    /**
     * Rotas de autenticação protegidas
     * 
     * Operações que requerem usuário autenticado
     */
    $router->post('/auth/logout', 'App\Http\Controllers\AuthController@logout');
    $router->get('/auth/me', 'App\Http\Controllers\AuthController@me');
    $router->post('/auth/revoke-all-tokens', 'App\Http\Controllers\AuthController@revokeAllTokens');

    /**
     * Rotas de produtos
     * 
     * CRUD de produtos do sistema
     */
    $router->get('/products', 'App\Http\Controllers\ProductController@index');
    $router->post('/products', 'App\Http\Controllers\ProductController@store');

    /**
     * Rotas de estoque
     * 
     * Controle de estoque de produtos
     */
    $router->get('/inventory', 'App\Http\Controllers\InventoryController@index');
    $router->post('/inventory', 'App\Http\Controllers\InventoryController@store');

    /**
     * Rotas de vendas
     * 
     * Processamento e consulta de vendas
     */
    $router->post('/sales', 'App\Http\Controllers\SalesController@store');
    $router->get('/sales/{id}', 'App\Http\Controllers\SalesController@show');
});

/**
 * ========================================
 * INJEÇÃO DE DEPENDÊNCIAS
 * ========================================
 * 
 * Configuração de bindings para injeção de dependências.
 * Utiliza o padrão Singleton para garantir uma única instância
 * de cada serviço durante o ciclo de vida da requisição.
 */

/**
 * Bindings para serviços de estoque
 */
$app->singleton(
    App\Interfaces\InventoryServiceInterface::class,
    App\Services\InventoryService::class
);

$app->singleton(
    App\Interfaces\InventoryRepositoryInterface::class,
    App\Respositories\InventoryRepository::class
);

/**
 * Bindings para serviços de vendas
 */
$app->singleton(
    App\Interfaces\SalesServiceInterface::class,
    App\Services\SalesService::class
);

$app->singleton(
    App\Interfaces\SalesRepositoryInterface::class,
    App\Respositories\SalesRepository::class
);

/**
 * Bindings para serviços de produtos
 */
$app->singleton(
    App\Interfaces\ProductServiceInterface::class,
    App\Services\ProductService::class
);

$app->singleton(
    App\Interfaces\ProductRepositoryInterface::class,
    App\Respositories\ProductRepository::class
);

/**
 * ========================================
 * RETORNO DA APLICAÇÃO
 * ========================================
 * 
 * Retorna a instância configurada da aplicação Laravel
 * para ser utilizada pelo servidor web.
 */
return $app;
