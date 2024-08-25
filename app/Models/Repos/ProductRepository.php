<?php

namespace App\Models\Repos;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductRepository extends ModelRepository
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
