<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariants extends ProductRepository
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

    /**
     * Get the primary image for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get many images for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get all prices for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Get all inventories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory()
    {
        return $this->hasMany(ProductInventory::class);
    }


    /**
     * Get current price for this product instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currentPrice()
    {
        $now = now();

        // First find a price that does not have a "Regular" price type
        $currentPrice = $this->prices()
            ->where('locale', app()->getLocale())
            ->where('price_type', '!=', 'Regular') // not "Regular" !
            ->where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->where('end_date', '>=', $now)
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'asc')
            ->first();
        
        // !!! FALLBACK !!! - If no price with another `price type` was found, take the "Regular" price
        if (!$currentPrice) {
            $currentPrice = $this->prices()
                ->where('locale', app()->getLocale())
                ->where('price_type', 'Regular')
                ->where('start_date', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->where('end_date', '>=', $now)
                        ->orWhereNull('end_date');
                })
                ->orderBy('created_at', 'asc')
                ->first();
        }
        
        return $currentPrice->price ?? null;
        
    }

    /**
     * Get full price ("Regular") for this product instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fullPrice()
    {
        $now = now();

        $fullPrice = $this->prices()
            ->where('locale', app()->getLocale())
            ->where('price_type', 'Regular')
            ->where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->where('end_date', '>=', $now)
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return $fullPrice->price ?? null;
    }

    /**
     * Get currency for full price ("Regular") for this product instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fullPriceCurrency()
    {
        $now = now();

        $fullPrice = $this->prices()
            ->where('locale', app()->getLocale())
            ->where('price_type', 'Regular')
            ->where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->where('end_date', '>=', $now)
                    ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return $fullPrice->currency ?? null;
    }
}
