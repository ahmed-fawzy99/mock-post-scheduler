<?php

namespace App\Http\Filters\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    public Builder $builder;
    public Request $request;
    protected array $sortable;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function filter($params)
    {
        foreach ($params as $key => $value ){
            if (method_exists($this, $key)){
                $this->$key($value);
            }
        }
    }

    protected function sort($value)
    {
        $columns = explode(',', $value);
        foreach ($columns as $column){
            $dir = 'asc';
            if (str_starts_with($column, '-')){
                $dir = 'desc';
                $column = substr($column, 1);
            }
            $dbColName = $this->sortable[$column] ?? $column;
            $this->builder->orderBy($dbColName, $dir);
        }
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $key => $value ){
            if (method_exists($this, $key)){
                $this->$key($value);
            }
        }
    }

}
