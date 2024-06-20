<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class VerifyEmailToken extends Model
{
    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
