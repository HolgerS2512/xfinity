<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Translation;
use App\Scopes\WithOrderByRankingScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Category extends ModelRepository
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ranking',
        'name',
        'level',
        'parent_id',
        'description',
        'active',
        'popular',
        'updated_at',
    ];

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

        // Listen to the "creating" event
        static::creating(function ($thisInstance) {
            $hash = (string) Str::uuid();
            $saved = static::saveTranslation($thisInstance->attributes['name'], $hash);
            if ($saved) {
                $thisInstance->name =  $hash;
            }

            if (isset($thisInstance->attributes['description']) && !empty($thisInstance->attributes['description'])) {
                $hash = (string) Str::uuid();
                $saved = static::saveTextTranslation($thisInstance->attributes['description'], $hash);
                if ($saved) {
                    $thisInstance->description =  $hash;
                }
            }
        });

        // This model always sorts by ranking
        static::addGlobalScope(new WithOrderByRankingScope);
    }

    /**
     * Scope a query to only include active categories.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
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
     * Get the subcategories for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all of the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function products(): HasMany|NULL
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the parent category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function parentCategory(): BelongsTo|NULL
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Load all categories with the same level and active status, including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function loadActiveCategoriesByLvl($level = 1)
    {
        $makeHiddenAttr = [
            // 'id',
            'parent_id',
            'ranking',
            'description',
            'popular',
            'created_at',
            'updated_at',
        ];

        // Retrieve all active categories at the specified level
        $categories = static::where('level', $level)
            ->active()
            ->with(['subcategories' => function ($query) use ($level) {
                // Load active subcategories recursively
                $query->active()
                    ->with(['subcategories' => function ($query) use ($level) {
                        // Load deeper levels of active subcategories
                        $query->active();
                    }]);
            }])
            ->get();

        // Recursively load subcategories for each category
        foreach ($categories as $category) {
            $category->subcategories = static::loadSubcategoriesRecursive($category->subcategories);

            // Exclude 'id' and 'created_at' attributes for the category and its subcategories
            $category->makeHidden($makeHiddenAttr);

            // Also hide attributes for all nested subcategories
            foreach ($category->subcategories as $subcategory) {
                $subcategory->makeHidden($makeHiddenAttr);
            }
        }

        return $categories;
    }

    /**
     * Load all categories with the same level and active status, including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function loadAllCategoriesByLvl($level = 1)
    {
        // Retrieve all active categories at the specified level
        $categories = static::where('level', $level)
            ->with(['subcategories' => function ($query) use ($level) {
                // Load active subcategories recursively
                $query->with('subcategories');
            }])
            ->get();

        // Recursively load subcategories for each category
        foreach ($categories as $category) {
            $category->subcategories = static::loadSubcategoriesRecursive($category->subcategories);
        }

        return $categories;
    }

    /**
     * Recursively load all active subcategories for a given collection of categories.
     *
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private static function loadSubcategoriesRecursive($categories)
    {
        // Recursively load all active subcategories for each category
        return $categories->map(function ($category) {
            $category->subcategories = $category->subcategories->map(function ($subcategory) {
                $subcategory->subcategories = static::loadSubcategoriesRecursive($subcategory->subcategories);
                return $subcategory;
            });
            return $category;
        });
    }

    /**
     * Save id as hash and a string in german column `Translation` model.
     * 
     * @param string $str
     * @param string $hash
     * @return bool
     */
    public static function saveTranslation($str, $hash): bool
    {
        try {
            $result = Translation::insert([
                'hash' => $hash,
                'de' => $str,
            ]);

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Save id as hash and a string in german column `TextTranslation` model.
     * 
     * @param string $str
     * @param string $hash
     * @return bool
     */
    public static function saveTextTranslation($str, $hash): bool
    {
        try {
            $result = TextTranslation::insert([
                'hash' => $hash,
                'de' => $str,
            ]);

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
