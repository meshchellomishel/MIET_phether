<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class FiltersArrayContain extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $query->whereJsonContains($column, $value);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        $query->where(function (Builder $query) use ($values, $column) {
            foreach ($values as $value) {
                $query->orWhereJsonContains($column, $value);
            }
        });
    }
}
