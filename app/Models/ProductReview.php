<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends ProductRepository
{
    /**
     * Get the product associated with the review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who wrote the review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who wrote the review.
     *
     * @return \App\Models\User
     */
    public function getUser()
    {
        // Retrieve the user who wrote this review
        return $this->user;
    }

    /**
     * Get the product associated with the review.
     *
     * @return \App\Models\Product
     */
    public function getProduct()
    {
        // Retrieve the product that this review is associated with
        return $this->product;
    }
}
