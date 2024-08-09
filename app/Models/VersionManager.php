<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VersionManager extends Model
{
    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * Indicates that the primary key for the table is the 'hash' column
     *
     */
    protected $primaryKey = 'id';

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
    protected $fillable = [
        'id',
        'hash',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();

        // Listen to the "creating" event
        static::creating(function ($thisInstance) {
            $thisInstance->id = static::makeHash(30);
            $thisInstance->hash = static::makeHash();
        });

        // Listen to the "updating" event
        static::updating(function ($thisInstance) {
            $thisInstance->hash = static::makeHash();
        });
    }

    /**
     * Returned a hash as string.
     * 
     * @return string $result
     */
    public static function makeHash($chars = 40)
    {
        $random = Str::random($chars);
        $date = preg_replace('/[^0-9]+/', '', trim(Carbon::now()));
        $hash = mb_substr(Hash::make($random), 7) . $date;
        $str = str_replace('.', '', $hash);

        return substr($str, -$chars, $chars);
    }
}
