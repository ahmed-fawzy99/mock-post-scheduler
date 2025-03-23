<?php

namespace App\Http\Filters\Api\V1\Filters;

use App\Http\Filters\Api\V1\QueryFilter;
use Illuminate\Support\Facades\DB;

class UserFilter extends QueryFilter
{
    protected array $sortable = [
        'id',
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function include(string $relationship)
    {
        return $this->builder->with($relationship);
    }

    public function search($value, $columns = ['name', 'email'])
    {
        $operator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        return $this->builder->where(function ($query) use ($value, $columns, $operator) {
            foreach ($columns as $column) {
                $query->orWhere($column, $operator, "%{$value}%");
            }
        });
    }

    public function name($value)
    {
        $operator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        return $this->builder->where('name', $operator, "%{$value}%");
    }
    public function email($value)
    {
        $operator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        return $this->builder->where('email', $operator, "%{$value}%");
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
