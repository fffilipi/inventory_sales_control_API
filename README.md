# Inventory Sales Control API

API de Controle de Estoque e Vendas

## About

This is a Laravel-based API for inventory and sales control management. The application provides endpoints for managing products, inventory, sales, and related business operations.

## Features

- Product management
- Inventory tracking
- Sales processing
- User authentication
- RESTful API endpoints

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 11.x

## Installation

1. Clone the repository

Subir containers com Sail

O Laravel Sail é uma interface de linha de comando leve para gerenciar um ambiente de desenvolvimento Docker do Laravel.
Ele já vem configurado com serviços como MySQL, Redis, MailHog e outros, facilitando o uso sem precisar configurar manualmente containers.

Este comando inicia todos os serviços definidos no docker-compose.yml em modo detached (em segundo plano).
./vendor/bin/sail up -d

Encerra e remove todos os containers, redes e volumes temporários criados pelo Sail.
./vendor/bin/sail down

Lista os containers ativos do ambiente atual.
./vendor/bin/sail ps

No projeto atual, estão sendo utilizados:
MySQL – Banco de dados principal
Redis – Cache e fila de tarefas

Testar conexão com o Redis
Abra o Tinker dentro do container principal (por exemplo, inventory_api):

docker compose exec inventory_api php artisan tinker
- cache()->store('redis')->put('teste', 'ok', 10);
- cache()->store('redis')->get('teste');


2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Start the development server: `php artisan serve`

## API Documentation

API documentation will be available once the endpoints are implemented.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).