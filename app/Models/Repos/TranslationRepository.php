<?php

namespace App\Models\Repos;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationRepository extends Model
{
    use HasFactory;

    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * Indicates that the primary key for the table is the 'hash' column
     *
     */
    protected $primaryKey = 'hash';

    /**
     * Disables auto-incrementing since the primary key is not an integer
     *
     */
    public $incrementing = false;

    /**
     * Specifies the type of the primary key as a string
     *
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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
