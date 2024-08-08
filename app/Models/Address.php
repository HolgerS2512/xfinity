<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Models\Repos\ModelRepository;

class Address extends ModelRepository
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'address_type' => AddressType::class,
    ];
}
