<?php

namespace App\Services;

use App\Interfaces\ProductServiceInterface;
use App\Respositories\ProductRepository;
use App\DTOs\Product\ProductDTO;

/**
 * Serviço responsável pela lógica de negócio dos produtos
 * 
 * Este serviço implementa a camada de lógica de negócio para produtos,
 * atuando como intermediário entre os controllers e repositories.
 * Centraliza as regras de negócio relacionadas aos produtos.
 * 
 * @package App\Services
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class ProductService implements ProductServiceInterface
{
    /**
     * Repository de produtos injetado via dependência
     * 
     * @var ProductRepository
     */
    protected $repository;

    /**
     * Construtor do serviço
     * 
     * @param ProductRepository $repository Repository de produtos
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Cria um novo produto no sistema
     * 
     * @param ProductDTO $productDTO Dados do produto
     * @return ProductDTO Produto criado
     * @throws \Exception Quando ocorre erro na criação
     */
    public function createProduct(ProductDTO $productDTO): ProductDTO
    {
        $product = $this->repository->create($productDTO->toModelData());
        return ProductDTO::fromModel($product);
    }

    /**
     * Retorna todos os produtos cadastrados
     * 
     * @return array Lista de produtos como DTOs
     */
    public function getAllProducts(): array
    {
        $products = $this->repository->getAll();
        return $products->map(function ($product) {
            return ProductDTO::fromModel($product);
        })->toArray();
    }

    /**
     * Busca um produto específico por ID
     * 
     * @param int $id ID do produto
     * @return ProductDTO Produto encontrado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando produto não existe
     */
    public function getProduct(int $id): ProductDTO
    {
        $product = $this->repository->find($id);
        return ProductDTO::fromModel($product);
    }
}
