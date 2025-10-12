<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de produto do sistema
 * 
 * Este modelo representa os produtos cadastrados no sistema de controle
 * de estoque e vendas, incluindo informações de preços e relacionamentos.
 * 
 * @package App\Models
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 * 
 * @property int $id
 * @property string $sku Código único do produto
 * @property string $name Nome do produto
 * @property string|null $description Descrição do produto
 * @property float $cost_price Preço de custo
 * @property float $sale_price Preço de venda
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'sku', 'name', 'description', 'cost_price', 'sale_price'
    ];

    /**
     * Relacionamento com o estoque do produto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}
