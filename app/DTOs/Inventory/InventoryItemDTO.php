<?php

namespace App\DTOs\Inventory;

use App\DTOs\BaseDTO;

/**
 * DTO para item de estoque
 * 
 * Este DTO encapsula os dados de um item individual no estoque,
 * incluindo informações do produto e quantidade.
 * 
 * @package App\DTOs\Inventory
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class InventoryItemDTO extends BaseDTO
{
    /**
     * ID do produto
     * 
     * @var int
     */
    public int $product_id;

    /**
     * Quantidade em estoque
     * 
     * @var int
     */
    public int $quantity;

    /**
     * Construtor do DTO de item de estoque
     * 
     * @param int $product_id ID do produto
     * @param int $quantity Quantidade em estoque
     */
    public function __construct(int $product_id, int $quantity)
    {
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data Dados para criar o DTO
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            $data['product_id'],
            $data['quantity']
        );
    }

    /**
     * Cria uma instância do DTO a partir de um modelo Inventory
     * 
     * @param \App\Models\Inventory $inventory Modelo do estoque
     * @return static
     */
    public static function fromModel($inventory): static
    {
        return new static(
            $inventory->product_id,
            $inventory->quantity
        );
    }

    /**
     * Retorna os dados para criação/atualização no banco
     * 
     * @return array
     */
    public function toModelData(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
        ];
    }
}
