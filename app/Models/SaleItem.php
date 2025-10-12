<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de item de venda do sistema
 * 
 * Este modelo representa os itens individuais de uma venda,
 * contendo informações sobre produto, quantidade e preços unitários.
 * 
 * @package App\Models
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 * 
 * @property int $id
 * @property int $sale_id ID da venda
 * @property int $product_id ID do produto
 * @property int $quantity Quantidade vendida
 * @property float $unit_price Preço unitário de venda
 * @property float $unit_cost Custo unitário
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SaleItem extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'unit_price', 'unit_cost'
    ];

    /**
     * Relacionamento com a venda
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relacionamento com o produto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
