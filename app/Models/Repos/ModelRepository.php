<?php

namespace App\Models\Repos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ModelRepository extends Model implements AuditableContract
{
    use HasFactory, Auditable;

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
