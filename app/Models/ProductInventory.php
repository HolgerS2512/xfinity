<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ProductInventory extends ModelRepository
{
    /**
     * The "booted" method of the model.
     *
     * This method is called when the model is booted, and we can apply
     * a global scope here to always order by `ranking`.
     */
    protected static function booted()
    {
        static::addGlobalScope('orderByRanking', function (Builder $builder) {
            $builder->orderBy('ranking');
        });
    }

    /**
     * Get the product variant associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
