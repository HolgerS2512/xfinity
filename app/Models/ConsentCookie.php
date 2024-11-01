<?php

namespace App\Models;

use App\Models\Repos\CookieRepository;

class ConsentCookie extends CookieRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'consent_id',
        'cookie_id',
        'consented',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'consented' => 'boolean',
    ];
}
