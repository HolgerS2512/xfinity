<?php

namespace App\Models;

use App\Models\Repos\CookieRepository;

class Consent extends CookieRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'consent_token',
        'ip_address',
        'user_agent',
        'consent_given',
        'updated_at',
    ];
}
