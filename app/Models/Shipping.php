<?php

namespace App\Models;

use App\Enums\ShippingMethod;
use App\Enums\ShippingStatus;
use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends ModelRepository
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, class>
     */
    protected $casts = [
        'shipping_method' => ShippingMethod::class,
        'status' => ShippingStatus::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     * 
     * This is required for the SoftDeletes trait, as it relies on 
     * the 'deleted_at' timestamp to determine whether a record 
     * has been soft deleted or not.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
