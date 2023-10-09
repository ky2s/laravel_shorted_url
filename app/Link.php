<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * Class Link
 *
 * @mixin Builder
 * @package App
 */
class Link extends Model
{
    protected $dates = ['ends_at', 'created_at', 'updated_at'];

    protected $casts = [
        'country_target' => 'object',
        'platform_target' => 'object',
        'language_target' => 'object',
        'rotation_target' => 'object'
    ];

    public function getTotalClicksAttribute()
    {
        return $this->hasMany('App\Stat')->where('link_id', $this->id)->count();
    }

    public function space()
    {
        return $this->hasOne('App\Space', 'id', 'space_id');
    }

    public function domain()
    {
        return $this->hasOne('App\Domain', 'id', 'domain_id');
    }

    public function stats()
    {
        return $this->hasMany('App\Stat')->where('link_id', $this->id);
    }

    public function user()
    {
        return $this->belongsTo('App\User')->where('id', $this->user_id)->withTrashed();
    }

    public function pixels() {
        return $this->belongsToMany('App\Pixel');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchTitle(Builder $query, $value)
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchUrl(Builder $query, $value)
    {
        return $query->where('url', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchAlias(Builder $query, $value)
    {
        return $query->where('alias', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchSpace(Builder $query, $value)
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchDomain(Builder $query, $value)
    {
        return $query->where('domain_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearchExpired(Builder $query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('ends_at')
                ->where('ends_at', '<', Carbon::now());
        })->orWhere(function ($query) {
            $query->where('expiration_clicks', '>', 0)
                ->whereColumn('clicks', '>=', 'expiration_clicks');
        });
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearchDisabled(Builder $query)
    {
        return $query->where('disabled', '=', 1);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearchActive(Builder $query)
    {
        return $query->where('disabled', '=', 0)
            ->where(function ($query) {
                $query->where('ends_at', '>', Carbon::now())
                    ->orWhere('ends_at', '=', null);
            })
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('expiration_clicks', '=', null)
                        ->orWhere('expiration_clicks', '=', 0);
                })
                ->orWhere(function ($query) {
                    $query->where('expiration_clicks', '>', 0)
                        ->whereColumn('clicks', '<', 'expiration_clicks');
                });
            });
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeUserId(Builder $query, $value)
    {
        return $query->where('user_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSpaceId(Builder $query, $value)
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeDomainId(Builder $query, $value)
    {
        return $query->where('domain_id', '=', $value);
    }

    /**
     * Encrypt the link's password
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the link's password
     *
     * @param $value
     * @return string
     */
    public function getPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Encrypt the link's stats password
     *
     * @param $value
     */
    public function setPrivacyPasswordAttribute($value)
    {
        $this->attributes['privacy_password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the link's stats page password
     *
     * @param $value
     * @return string
     */
    public function getPrivacyPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
