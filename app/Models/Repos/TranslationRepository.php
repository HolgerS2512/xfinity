<?php

namespace App\Models\Repos;


class TranslationRepository extends ModelRepository
{
    /**
     * Indicates that the primary key for the table is the 'hash' column
     *
     */
    protected $primaryKey = 'id';

    /**
     * Disables auto-incrementing since the primary key is not an integer
     *
     */
    public $incrementing = false;

    /**
     * Specifies the type of the primary key as a string
     *
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Eloquent Event Listener
     *
     */
    protected static function boot()
    {
        parent::boot();
    }
}
