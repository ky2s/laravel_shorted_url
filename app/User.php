<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

/**
 * Class User
 *
 * @mixin Builder
 * @package App
 */
class User extends Authenticatable implements MustVerifyEmail, hasLocalePreference
{
    use MustVerifyNewEmail, Notifiable, Billable, SoftDeletes;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the tax rates to apply to the subscription.
     *
     * @return array
     */
    public function taxRates()
    {
        return config('stripe.tax_rates');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchName(Builder $query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchEmail(Builder $query, $value)
    {
        return $query->where('email', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchRole(Builder $query, $value)
    {
        return $query->where('role', '=', $value);
    }

    /**
     * Get the preferred locale of the entity.
     *
     * @return string|null
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    /**
     * Get the user's links
     */
    public function links()
    {
        return $this->hasMany('App\Link')->where('user_id', $this->id);
    }

    /**
     * Get the user's spaces
     */
    public function spaces()
    {
        return $this->hasMany('App\Space')->where('user_id', $this->id);
    }

    /**
     * Get the user's domains
     */
    public function domains()
    {
        return $this->hasMany('App\Domain')->where('user_id', $this->id);
    }

    /**
     * Get the user's pixels
     */
    public function pixels()
    {
        return $this->hasMany('App\Pixel')->where('user_id', $this->id);
    }

    /**
     * Get the user's pixels
     */
    public function linksPixels()
    {
        return $this->hasManyThrough('App\LinkPixel', 'App\Pixel', 'user_id', 'pixel_id', 'id', 'id');
    }

    /**
     * Get the stats stored data for this user
     */
    public function stats()
    {
        return $this->hasManyThrough('App\Stat', 'App\Link', 'user_id', 'link_id', 'id', 'id');
    }
}
