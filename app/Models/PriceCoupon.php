<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceCoupon extends ModelRepository
{
    use SoftDeletes;

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

    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();
    }
}
