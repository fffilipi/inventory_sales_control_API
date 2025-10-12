<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de estoque do sistema
 * 
 * Este modelo representa o controle de estoque dos produtos,
 * mantendo a quantidade disponível e informações de atualização.
 * 
 * @package App\Models
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 * 
 * @property int $id
 * @property int $product_id ID do produto
 * @property int $quantity Quantidade em estoque
 * @property \Carbon\Carbon|null $last_updated Data da última atualização
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Inventory extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     * 
     * @var string
     */
    protected $table = 'inventory';

    /**
     * Atributos que podem ser preenchidos em massa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'product_id', 'quantity', 'last_updated'
    ];

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
