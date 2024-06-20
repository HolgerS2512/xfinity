<?php

namespace App\Traits\Api;

use App\Models\Auth\PersonalAccessUrlCodeFactory;

trait HasApiUrlCodeTrait
{
    /**
     * Return a new PersonalAccessUrlCodeFactory class.
     *
     * @return \App\Models\Auth\PersonalAccessUrlCodeFactory
     */
    public static function createUrlCode($length = 50)
    {
        return new PersonalAccessUrlCodeFactory($length);
    }
}