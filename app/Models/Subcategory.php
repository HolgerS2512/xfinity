<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcategory extends Model
{
    use HasFactory;

    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'ranking',
        'name',
        'active',
        'popular',
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
        static::creating(function ($subcategory) {
            // Transform Name
            $subcategory->name = static::transformString($subcategory->name);
        });

        // Listen to the "updating" event
        static::updating(function ($subcategory) {
            $subcategory->name = static::transformString($subcategory->name);
        });

        // This model always sorts by ranking
        static::addGlobalScope('orderByRanking', function (Builder $builder) {
            $builder->orderBy('ranking');
        });
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
     * Return the Category for this Subcategory
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function transformString($string, $char = '_')
    {
        // Removes special characters
        $result = str_replace('&', 'and', $string);
        $result = preg_replace('/[^A-Za-z0-9-]+/', $char, $result);
        // All to lower case
        $result = strtolower($result);
        // Removing leading and trailing hyphens
        $result = trim($result, $char);

        return $result;
    }
}
