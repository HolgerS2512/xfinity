<?php

namespace App\Traits\Api;

use App\Models\Auth\PersonalAccessCodeFactory;

trait GetApiCodesTrait
{
    /**
     * Return a new PersonalAccessCodeFactory class.
     *
     * @return \App\Models\Auth\PersonalAccessCodeFactory
     */
    public static function create($length = 50)
    {
        return new PersonalAccessCodeFactory($length);
    }
}
