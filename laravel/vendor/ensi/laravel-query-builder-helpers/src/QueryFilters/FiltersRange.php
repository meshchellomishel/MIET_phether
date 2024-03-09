<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Ensi\QueryBuilderHelpers\Enums\RangeOperator;
use Illuminate\Database\Eloquent\Builder;

class FiltersRange extends FiltersBase
{
    public function __construct(protected readonly RangeOperator $operator, bool $addRelationConstraint = true)
    {
        parent::__construct($addRelationConstraint);
    }

    protected function applyOne(Builder $query, $value, string $column): void
    {
        $query->where($column, $this->operator->value, $value);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        $query->where(function (Builder $query) use ($values, $column) {
            foreach (array_filter($values) as $value) {
                $query->orWhere($column, $this->operator->value, $value);
            }
        });
    }
}
