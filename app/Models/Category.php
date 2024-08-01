<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Subcategory;

class Category extends Model
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
        static::creating(function ($category) {
            // Transform Name
            $category->name = static::transformString($category->name);
        });

        // Listen to the "updating" event
        static::updating(function ($category) {
            $category->name = static::transformString($category->name);
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
     * Get the subcategory for this Category.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    /**
     * Get the subcategory for this Category.
     * 
     * @param string $string
     * @param string default $char = '_'
     * @return string $result
     */
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
