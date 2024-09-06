<?php

namespace App\Models;

use App\Enums\PaymentMethodType;
use App\Models\Repos\ModelRepository;

class PaymentMethod extends ModelRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class>
     */
    protected $casts = [
        'type' => PaymentMethodType::class,
    ];

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
