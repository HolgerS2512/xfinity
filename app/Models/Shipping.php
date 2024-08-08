<?php

namespace App\Models;

use App\Enums\ShippingMethod;
use App\Enums\ShippingStatus;
use App\Models\Repos\ModelRepository;

class Shipping extends ModelRepository
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class>
     */
    protected $casts = [
        'shipping_method' => ShippingMethod::class,
        'status' => ShippingStatus::class,
    ];
}
