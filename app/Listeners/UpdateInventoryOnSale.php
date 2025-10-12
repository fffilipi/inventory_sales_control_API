<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;

/**
 * Listener responsável por atualizar o estoque quando uma venda é finalizada
 * 
 * Este listener é executado automaticamente quando o evento SaleCompleted
 * é disparado, atualizando o estoque dos produtos vendidos. Implementa
 * mecanismo de cache para evitar processamento duplo e garante a
 * consistência dos dados de estoque.
 * 
 * @package App\Listeners
 * @author Sistema de Controle de Estoque e Vendas
 * @version 1.0.0
 */
class UpdateInventoryOnSale
{
    /**
     * Processa o evento de venda finalizada e atualiza o estoque
     * 
     * Este método é executado automaticamente quando uma venda é completada.
     * Reduz a quantidade em estoque para cada produto vendido, implementando
     * um sistema de cache para evitar processamento duplo do mesmo evento.
     * 
     * @param SaleCompleted $event Evento de venda finalizada
     * @return void
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Quando produto não possui estoque
     * 
     * @example
     * O listener é executado automaticamente quando SaleCompleted::dispatch($sale) é chamado
     */
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;
        
        // Verificar se a venda já foi processada (evitar processamento duplo)
        $cacheKey = "sale_processed_{$sale->id}";
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            Log::warning("Sale ID {$sale->id} already processed, skipping inventory update");
            return;
        }
        
        // Marcar venda como processada (cache por 1 hora)
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, 3600);
        
        Log::info('Processing inventory update for sale ID: ' . $sale->id);

        // Atualizar estoque para cada item da venda
        foreach ($sale->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->firstOrFail();
            
            // Reduzir a quantidade do estoque
            $inventory->quantity -= $item->quantity;
            $inventory->last_updated = now();
            $inventory->save();
            
            Log::info("Updated inventory for product {$item->product_id}: reduced {$item->quantity} units. New quantity: {$inventory->quantity}");
        }
        
        Log::info('Inventory update completed for sale ID: ' . $sale->id);
    }
}
