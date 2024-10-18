<?php

namespace App\Models;

use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Scopes\WithOrderByRankingScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class Category extends ModelRepository
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
        'name',
        'description',
        'slug',
        'products',
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

    /**
     * Get the category name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $translation = $this->translations()->firstWhere('locale', app()->getLocale());
        $deTrans = $this->translations()->firstWhere('locale', 'de');

        return $translation ? $translation->name : $deTrans;
    }

    /**
     * Get the category description.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $translation = $this->translations()->firstWhere('locale', app()->getLocale());
        $deTrans = $this->translations()->firstWhere('locale', 'de');

        return $translation ? $translation->description : $deTrans;
    }

    /**
     * Get the category slug.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return self::makeSlugByName($this->name);
    }

    /**
     * Get the category slug.
     *
     * @return string
     */
    public function getProductsAttribute()
    {
        return $this->products();
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
     * Get the active subcategories for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->active();
    }

    /**
     * Returns a bool - does this category have products (using database query if not loaded).
     *
     * @return bool
     */
    public function hasProducts()
    {
        return $this->products->count() > 0;
    }

    /**
     * Get the active subcategories for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allSubcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the active subcategories for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allValues()
    {
        return $this->hasMany(Category::class, 'parent_id')->with(['translations']);
    }

    /**
     * Get all of the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id')
            ->whereNull('products.deleted_at');
    }

    /**
     * Get the parent category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|null
     */
    public function parentCategory()
    {
        return $this->belongsToMany(Category::class, 'parent_id');
    }

    /**
     * Load all categories with the same level and active status, including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function loadActiveByLvl($shouldHidden = [], $level = 1)
    {
        // Retrieve all active categories at the specified level
        $categories = static::where('level', $level)->get();

        // Filter the collection based on the active status
        $filtered = $categories->filter(fn($model) => $model->active);

        return self::makeRecursiveHidden($shouldHidden, $filtered, 'subcategories');
    }

    /**
     * Load all category values and children including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function loadActiveChildsHidden($shouldHidden)
    {
        return self::makeClassRecursiveHidden($shouldHidden, $this, 'subcategories');
    }

    /**
     * Load all category values and children including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function loadAllChildsById($id, $shouldHidden)
    {
        // Retrieve all active categories at the specified level
        $category = static::findOrFail($id);

        return self::makeClassRecursiveHidden($shouldHidden, $category, 'allSubcategories');
    }

    /**
     * Load all categories with the same level and active status, including their subcategories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function loadAllCategoriesByLvl($shouldHidden = [], $level = 1)
    {
        // Retrieve all categories at the specified level
        $categories = static::where('level', $level)->get();

        return self::makeClassRecursiveHidden($shouldHidden, $categories, 'allValues');
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

    /**
     * Recursively load all childs for a given collection and make gived attr hidden.
     *
     * @param array $hiddenAttr | contains attributes to be hidden
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param string $methodName | child function called
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected static function makeRecursiveHidden($hiddenAttr, $collection, $methodName)
    {
        foreach ($collection as $model) {
            // Make the specified attributes hidden
            $model->makeHidden($hiddenAttr);

            // Check if the method exists and get the children
            if (method_exists($model, $methodName)) {
                $children = $model->$methodName;

                // Ensure children are a collection and not empty
                if ($children instanceof \Illuminate\Database\Eloquent\Collection && !$children->isEmpty()) {
                    // Recursively apply the hidden attributes to children
                    self::makeRecursiveHidden($hiddenAttr, $children, $methodName);
                }
            }
        }

        return $collection;
    }

    /**
     * Recursively load all childs for a given collection and make gived attr hidden.
     *
     * @param \App\Models\Model $model
     * @param string $methodName | child function called
     * @return \App\Models\Model
     */
    protected static function makeClassRecursiveHidden($hiddenAttr, $model, $methodName)
    {
        // Make the specified attributes hidden
        $model->makeHidden($hiddenAttr);

        if (method_exists($model, $methodName)) {
            $children = $model->$methodName;

            // Ensure children are a collection and not empty
            if ($children instanceof \Illuminate\Database\Eloquent\Collection && !$children->isEmpty()) {
                // Recursively apply the hidden attributes to children

                foreach ($children as $child) {
                    self::makeClassRecursiveHidden($hiddenAttr, $child, $methodName);
                }
            }
        }

        return $model;
    }

    /**
     * Retunred an url slug.
     *
     * @param string $name
     * @return string
     */
    public static function makeSlugByName($name): string
    {
        return preg_replace('/\s+/', '-', strtolower($name));
    }
}
