<?php

namespace App\Models\Repos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRepository extends Model
{
    use HasFactory;

    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;
}
