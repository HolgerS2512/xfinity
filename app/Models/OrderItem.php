<?php

namespace App\Models;

use App\Models\Repos\OrderRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends OrderRepository
{
    /**
     * Get the order for this item.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
