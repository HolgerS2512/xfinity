<?php

namespace App\Models;

use App\Enums\PermissionType;
use App\Models\Repos\ModelRepository;

class Permission extends ModelRepository
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => PermissionType::class,
    ];

    /**
     * Set the 'name' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        // Check if the value is an instance of PermissionType, if so, save its value, otherwise save the raw string
        $this->attributes['name'] = $value instanceof PermissionType ? $value->value : $value;
    }

    /**
     * Get the 'name' attribute.
     *
     * @param  string  $value
     * @return PermissionType
     */
    public function getNameAttribute($value)
    {
        // Convert the string value from the database to a PermissionType instance
        return PermissionType::from($value);
    }

    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
