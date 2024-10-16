<?php

namespace App\Models;

use App\Models\Repos\TranslationRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends TranslationRepository
{
    /**
     * Get the product associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
