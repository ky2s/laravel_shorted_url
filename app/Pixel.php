<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Domain
 *
 * @mixin Builder
 * @package App
 */
class Pixel extends Model
{
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
    public function scopeSearchType(Builder $query, $value)
    {
        return $query->where('type', '=', $value);
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

    public function getTotalLinksAttribute()
    {
        return $this->belongsToMany('App\Link')->count();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->where('id', $this->user_id)->withTrashed();
    }

    public function links() {
        return $this->belongsToMany('App\Link');
    }
}
