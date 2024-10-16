<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInfos extends ProductRepository
{
    /**
     * Get the product associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
