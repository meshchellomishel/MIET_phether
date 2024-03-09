<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class FiltersArrayExact extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $query->whereJsonContains($column, $value)->whereJsonLength($column, 1);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        foreach ($values as $value) {
            $query->whereJsonContains($column, $value);
        }

        $query->whereJsonLength($column, count($values));
    }
}
