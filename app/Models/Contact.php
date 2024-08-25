<?php

namespace App\Models;

use App\Enums\StatusValues;
use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends ModelRepository
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'salutation',
        'firstname',
        'lastname',
        'email',
        'phone',
        'message',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'status' => StatusValues::class,
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
}
