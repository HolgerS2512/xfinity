<?php

namespace App\Models\Repos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRepository extends Model
{
    use HasFactory;

    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();

        // Listen to the "updating" event
        static::updating(function ($thisInstance) {
            $thisInstance->updated_at = Carbon::now();
        });
    }
}
