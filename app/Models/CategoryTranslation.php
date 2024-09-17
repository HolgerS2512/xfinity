<?php

namespace App\Models;

use App\Models\Repos\TranslationRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends TranslationRepository
{
    /**
     * Get the product associated with the image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
