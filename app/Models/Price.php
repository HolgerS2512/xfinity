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
    public function ProductVariants(): BelongsTo
    {
        return $this->belongsTo(ProductVariants::class);
    }
}
