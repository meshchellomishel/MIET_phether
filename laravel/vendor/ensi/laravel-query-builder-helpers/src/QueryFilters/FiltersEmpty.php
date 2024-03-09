<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

class FiltersEmpty extends FiltersBase
{
    protected function applyOne(Builder $query, $value, string $column): void
    {
        true === filter_var($value, FILTER_VALIDATE_BOOL)
            ? $query->whereNull($column)
            : $query->whereNotNull($column);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        throw InvalidFilterValue::make(implode(', ', $values));
    }
}
