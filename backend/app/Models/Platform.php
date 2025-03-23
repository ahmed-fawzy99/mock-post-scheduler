<?php

namespace App\Models;

use App\Http\Filters\Api\V1\Filters\PlatformFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $guarded = [];

    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    public static function currentUserAllowedPlatforms()
    {
        return Platform::whereNotIn('id', auth()->user()->disallowedPlatforms->pluck('id'))->get();
    }

    public function scopeFilter(Builder $builder, PlatformFilter $filters)
    {
        return $filters->apply($builder);
    }
}
