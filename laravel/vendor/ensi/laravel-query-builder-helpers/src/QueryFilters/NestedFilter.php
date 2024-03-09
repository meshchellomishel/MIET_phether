<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NestedFilter extends AllowedFilter
{
    public function __construct(private readonly NestedScope $scope, AllowedFilter $source)
    {
        parent::__construct(
            $source->getName(),
            $source->filterClass,
            $source->getInternalName(),
        );

        $this->ignored = new Collection($source->getIgnored());
        $this->default($source->getDefault());
    }

    public function filter(QueryBuilder $query, $value): void
    {
        $valueToFilter = $this->resolveValueForFiltering($value);

        if (is_null($valueToFilter)) {
            return;
        }

        $this->scope
            ->attach($query->getEloquentBuilder())
            ->addFilter(fn (Builder $builder) => ($this->filterClass)($builder, $valueToFilter, $this->internalName));
    }
}
