<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

class FiltersHas extends FiltersBase
{
    public function __construct()
    {
        parent::__construct(false);
    }

    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        true === filter_var($value, FILTER_VALIDATE_BOOL)
            ? $query->doesntHave($column)
            : $query->has($column);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        throw InvalidFilterValue::make(implode(', ', $values));
    }

    protected function qualifyColumn(Builder $query, string $column): string
    {
        return $column;
    }
}
