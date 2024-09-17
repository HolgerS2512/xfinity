<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'level',
        'parent_id',
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
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name'
    ];

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
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();

        // This model always sorts by ranking
        static::addGlobalScope(new WithOrderByRankingScope);
    }

    /**
     * Eloquent Event Listener is booted
     *
     */
    protected static function booted()
    {
        // static::created(function ($category) {
        //     $category->createTranslation($category->attributes['translations']);
        // });
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

    // Accessor, um den Namen der Übersetzung als reguläres Attribut anzubieten
    public function getNameAttribute()
    {
        // Finde die Übersetzung basierend auf der aktuellen Locale
        $translation = $this->translations->firstWhere('locale', app()->getLocale());

        // Gib den Namen der Übersetzung zurück, oder einen Fallback (z.B. "name not found")
        return $translation ? $translation->name : 'Translation not available';
    }

    /**
     * Get all translations for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

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
            'parent_id',
            'ranking',
            'active',
            'popular',
            'created_at',
            'updated_at',
            'deleted_at',
            'translations',
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
     * Create a translation for the category.
     *
     * @param array $data The data for the translation, including 'name' and 'description'.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function createTranslation(array $data): bool
    {
        $check = [];

        foreach ($data as $translation) {
            // Use $this->translations() to create translations for the current category instance
            $check[] = $this->translations()->create([
                'locale' => $translation['locale'],
                'name' => $translation['name'],
                'description' => $translation['description'] ?? null,
            ]);
        }

        // Evaluate whether all translations were created successfully
        return !in_array(false, $check, true);
    }

    /**
     * Update or create a translation for a specific field.
     *
     * @param string $locale
     * @param array $data
     * @return bool
     */
    public function updateTranslation(array $data): bool
    {
        $check = [];

        foreach ($data as $translation) {

            // Check if there is an existing translation for this locale
            $currTlModel = $this->translations()->where('locale', $translation['locale'])->first();

            if ($currTlModel) {
                // Check if name exists
                if ($translation['name']) {
                    $currTlModel->name = $translation['name'];
                }

                // Check if description exists
                if ($translation['description']) {
                    $currTlModel->description = $translation['description'];
                }

                // Update the field with the new value
                $check[] = $currTlModel->save();
            } else {
                // Use $this->translations() to create translations for the current category instance
                $check[] = $this->translations()->create([
                    'locale' => $translation['locale'],
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }

        // Evaluate whether all translations were updated successfully
        return !in_array(false, $check, true);
    }
}
