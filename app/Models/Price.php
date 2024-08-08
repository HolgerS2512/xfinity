<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends ModelRepository
{
    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the product associated with the price.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product associated with the price.
     *
     * @return \App\Models\Product
     */
    public function getProduct()
    {
        return $this->product; // Retrieve the product that this price is associated with
    }
}
