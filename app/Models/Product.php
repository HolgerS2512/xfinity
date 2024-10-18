<?php

namespace App\Models;

use App\Models\Repos\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
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
        'name', 
        'description',
        'manufacturer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 
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
        $translation = $this->translations()->firstWhere('locale', app()->getLocale());
        $deTrans = $this->translations()->firstWhere('locale', 'de');

        return $translation ? $translation->name : $deTrans;
    }

    /**
     * Get the category description.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $translation = $this->translations()->firstWhere('locale', app()->getLocale());
        $deTrans = $this->translations()->firstWhere('locale', 'de');

        return $translation ? $translation->description : $deTrans;
    }

    /**
     * Get the category description.
     *
     * @return string
     */
    public function getManufacturerAttribute()
    {
        $rekord = $this->manufacturer()->firstWhere('id', $this->manufacturer_id)->only(['name', 'address', 'email', 'phone', 'url']);

        foreach ($rekord as $key => $value) {
            if (empty($value)) unset($rekord[$key]);
        }

        return $rekord;
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
        return $this->hasMany(ProductTranslation::class);
    }

    /**
     * Get many variants for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->hasMany(ProductVariants::class);
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

    /**
     * Get all reviews for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(ProductManufacturer::class);
    }

}
