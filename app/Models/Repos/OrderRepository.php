<?php

namespace App\Models\Repos;

use Illuminate\Database\Eloquent\Builder;

class OrderRepository extends ModelRepository
{
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
