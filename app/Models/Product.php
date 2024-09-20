<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use App\Scopes\WithImageScope;
use App\Scopes\WithPriceScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The `Produkt` model represents a product in the application.
 * It automatically includes the associated price through a global scope.
 */
final class Product extends ProductRepository
{
    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'prices',
        // 'primaryImage',
        // 'images',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'active',
        // 'popular',
        // 'pivot',
        // 'updated_at',
        // 'created_at',
        // 'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'popular' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     * This method is called after the model is instantiated.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Add the global scope to always load the `price` relationship
        // static::addGlobalScope(new WithPriceScope);
        // static::addGlobalScope(new WithImageScope);
    }

    /**
     * Scope a query to only include active products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the category name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $translation = $this->translations->firstWhere('locale', app()->getLocale());

        return $translation ? $translation->name : 'Translation not available';
    }

    /**
     * Get the category description.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $translation = $this->translations->firstWhere('locale', app()->getLocale());

        return $translation ? $translation->description : 'Translation not available';
    }

    /**
     * Get the translation.
     *
     * @return string
     */
    public function getTranslationAttribute()
    {
        return $this->translations()->where('locale', app()->getLocale())->first();
    }

    /**
     * Accessor to get the prices attribute from the related `Price` model.
     * 
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getPricesAttribute()
    {
        // Return the prices from the related `Price` model or null if not set
        return $this->prices ? $this->prices->prices : null;
    }

    /**
     * Accessor to get the mai image attribute from the related `ProductImage` model.
     * 
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getPrimaryImageAttribute()
    {
        // Return the main image from the related `ProductImage` model or null if not set
        return $this->primaryImage ? $this->primaryImage->primaryImage : null;
    }

    /**
     * Accessor to get the images attribute from the related `ProductImage` model.
     * 
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getImagesAttribute()
    {
        // Return the images from the related `ProductImage` model or null if not set
        return $this->images ? $this->images->images : null;
    }

    /**
     * Get all prices for the product.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrices()
    {
        return $this->prices; // Retrieve all prices associated with the product
    }

    /**
     * Get all reviews for the product.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReviews()
    {
        return $this->reviews; // Retrieve all reviews associated with the product
    }

    /**
     * Get all categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongstoMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')
            ->whereNull('categories.deleted_at');
    }

    /**
     * Get all translations for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
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
        return $this->hasMany(ProductImage::class)->where('is_primary', false);
    }

    /**
     * Get many details for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(ProductDetails::class)->where('locale', app()->getLocale());
    }

    /**
     * Get many infos for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function infos()
    {
        return $this->hasMany(ProductInfos::class)->where('locale', app()->getLocale());
    }

    /**
     * Get many order items for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get many wishlist item for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get all reviews for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
