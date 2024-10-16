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
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product associated with the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productVariants(): BelongsTo
    {
        return $this->belongsTo(ProductVariants::class);
    }
}
