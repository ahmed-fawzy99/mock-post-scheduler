<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;

class PlatformFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'name',
        'type',
        'characterLimit' => 'character_limit',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function include(string $relationship)
    {
        return $this->builder->with($relationship);
    }


    public function id($value)
    {
        $ids = explode(',', $value);
        if (count($ids) > 1) {
            return $this->builder->whereIn('id', $ids);
        }
        return $this->builder->where('id', $ids[0]);
    }

    public function createdAt($val)
    {
        $dates = explode(',', $val);
        if (count($dates) === 1) {
            return $this->builder->whereDate('created_at', $dates[0]);
        }
        return $this->builder->whereBetween('created_at', $dates);
    }

    public function updatedAt($val)
    {
        $dates = explode(',', $val);
        if (count($dates) === 1) {
            return $this->builder->whereDate('updated_at', $dates[0]);
        }
        return $this->builder->whereBetween('updated_at', $dates);
    }
}
