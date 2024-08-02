<?php

namespace App\Models\Repos;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class CategoryRepository extends Model
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

        // Listen to the "creating" event
        static::creating(function ($thisInstance) {
            $thisInstance->name = static::hashedTranslation($thisInstance->name);
        });

        // Listen to the "updating" event
        static::updating(function ($thisInstance) {
            $thisInstance->name = static::hashedTranslation($thisInstance->name);
        });

        // This model always sorts by ranking
        static::addGlobalScope('orderByRanking', function (Builder $builder) {
            $builder->orderBy('ranking');
        });
    }

    /**
     * Get the category's name.
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return __("shop.$value");
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'popular' => 'boolean',
    ];

    /**
     * Returned a hash as string.
     * 
     * @param string $string
     * @return string $result
     */
    public static function hashedTranslation($name)
    {
        $name = str_replace('shop.', '', $name);
        $date = preg_replace('/[^0-9]+/', '', trim(Carbon::now()));
        $hash = mb_substr(Hash::make($name), 7) . $date;

        try {
            $trans = Translation::insert([
                'hash' => $hash,
                'de' => $name,
            ]);

            if ($trans) {
                return $hash;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }




    /**
     * Returned a transform string.
     * 
     * @param string $string
     * @param string default $char = '_'
     * @return string $result
     */
    // public static function transformString($string, $char = '_')
    // {
    //     // Removes special characters
    //     $result = str_replace('&', 'and', $string);
    //     $result = preg_replace('/[^A-Za-z0-9-]+/', $char, $result);
    //     // All to lower case
    //     $result = strtolower($result);
    //     // Removing leading and trailing hyphens
    //     $result = trim($result, $char);

    //     return $result;
    // }
}
