<?php

namespace App\Models\Repos;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRepository extends ModelRepository
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
        // This model always sorts by created at
        static::addGlobalScope('orderByDate', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
}
