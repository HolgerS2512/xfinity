<?php

namespace App\Models;

use App\Enums\RoleType;
use App\Models\Repos\ModelRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends ModelRepository
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
        'name' => RoleType::class,
    ];

    /**
     * Set the 'name' attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        // Check if the value is an instance of RoleType, if so, save its value, otherwise save the raw string
        $this->attributes['name'] = $value instanceof RoleType ? $value->value : $value;
    }

    /**
     * Get the 'name' attribute.
     *
     * @param  string  $value
     * @return RoleType
     */
    public function getNameAttribute($value)
    {
        // Convert the string value from the database to a RoleType instance
        return RoleType::from($value);
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
