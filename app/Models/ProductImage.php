<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends ProductRepository
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_main' => 'boolean',
    ];

    /**
     * Get the product associated with the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
