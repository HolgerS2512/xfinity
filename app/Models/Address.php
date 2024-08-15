<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Models\Repos\ModelRepository;
use App\Scopes\WithOrderByCreateAscScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends ModelRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_type',
        'street',
        'house_number',
        'city',
        'state',
        'zip',
        'country',
        'active',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'user_id',
        'updated_at',
        'created_at',
    ];

    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new WithOrderByCreateAscScope);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'address_type' => AddressType::class,
    ];

    /**
     * Get the user for this address.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
