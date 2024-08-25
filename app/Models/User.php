<?php

namespace App\Models;

use App\Scopes\WithAddressScope;
use App\Scopes\WithOrderScope;
use App\Scopes\WithReviewScope;
use App\Scopes\WithWishlistScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    use HasApiTokens, HasFactory, Notifiable, Auditable;

    /**
     * Nullabled updated_at column by new instance.
     *
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'salutation',
        'firstname',
        'lastname',
        'birthday',
        'email',
        'password',
        'email_verified_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Eloquent Event Listener
     * This method is called after the model is instantiated.
     *
     */
    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new WithAddressScope);
        // static::addGlobalScope(new WithWishlistScope);
        // static::addGlobalScope(new WithOrderScope);
        // static::addGlobalScope(new WithReviewScope);
    }

    /**
     * Accessor to get the wishlist attribute from the related `WishlistItem` model.
     * Get all items in the user's wishlist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWishlistAttribute()
    {
        // Retrieve the wishlist and then get its items
        $wishlist = $this->getOrCreateWishlist();
        return $wishlist->items;
    }

    /**
     * Get many addresses for this user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|NULL
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get many orders for this user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all reviews written by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews(): HasMany|NULL
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the user's wishlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    /**
     * Retrieve or create the user's wishlist.
     *
     * @return \App\Models\Wishlist
     */
    public function getOrCreateWishlist()
    {
        // Retrieve the existing wishlist or create a new one if it doesn't exist
        return $this->getWishlist()->firstOrCreate();
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }
}
