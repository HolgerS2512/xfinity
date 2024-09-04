<?php

namespace App\Models;

use App\Enums\CookieType;
use App\Models\Repos\CookieRepository;

class Cookie extends CookieRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
        'category',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category' => CookieType::class,
    ];
}
