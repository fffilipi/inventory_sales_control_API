<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de venda do sistema
 * 
 * Este modelo representa as vendas realizadas no sistema,
 * incluindo valores totais, custos e margem de lucro calculados.
 * 
 * @package App\Models
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 * 
 * @property int $id
 * @property float $total_amount Valor total da venda
 * @property float $total_cost Custo total da venda
 * @property float $total_profit Lucro total da venda
 * @property string $status Status da venda (pending, completed)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Sale extends Model
{
    use HasFactory;

    /**
     * Atributos que podem ser preenchidos em massa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'total_amount', 'total_cost', 'total_profit', 'status'
    ];

    /**
     * Relacionamento com os itens da venda
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
