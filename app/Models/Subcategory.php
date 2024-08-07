<?php

namespace App\Models;

use App\Models\Repos\CategoryRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends CategoryRepository
{
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
     * Get the subcategory for this Category.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function maincategories(): HasMany
    {
        return $this->hasMany(MainCategory::class);
    }

    /**
     * Get the Category for this Subategory.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
