<?php

namespace App\Models;

use App\Models\Repos\CategoryRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MainCategory extends CategoryRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subcategory_id',
        'ranking',
        'name',
        'active',
        'popular',
        'updated_at',
    ];

    /**
     * Get the Category for this Subategory.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
