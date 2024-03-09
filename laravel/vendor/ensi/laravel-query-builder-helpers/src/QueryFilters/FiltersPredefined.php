<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

class FiltersPredefined extends FiltersBase
{
    public function __construct(private readonly Closure $callback, bool $addRelationConstraint = true)
    {
        parent::__construct($addRelationConstraint);
    }

    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        if (true === (bool) $value) {
            ($this->callback)($query);
        }
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        throw InvalidFilterValue::make(implode(', ', $values));
    }
}
