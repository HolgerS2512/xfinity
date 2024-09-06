<?php

namespace App\Models;

use App\Enums\StatusValues;
use App\Models\Repos\OrderRepository;
use App\Scopes\WithOrderItemsScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends OrderRepository
{
    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'items',
    ];

    /**
     * Eloquent Event Listener
     * This method is called after the model is instantiated.
     *
     */
    protected static function boot()
    {
        parent::boot();

        // Add the global scope to always load the `price` relationship
        static::addGlobalScope(new WithOrderItemsScope);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class>
     */
    protected $casts = [
        'status' => StatusValues::class,
    ];

    /**
     * Accessor to get the prices attribute from the related `Price` model.
     * 
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getItemsAttribute()
    {
        // Return the prices from the related `Price` model or null if not set
        return $this->items ? $this->items->items : null;
    }

    /**
     * Get many order items for this order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user for this order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get many payments for this order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
