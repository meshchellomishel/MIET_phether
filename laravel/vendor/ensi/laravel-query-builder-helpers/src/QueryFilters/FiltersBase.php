<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Filters\FiltersExact;

abstract class FiltersBase extends FiltersExact
{
    abstract protected function applyOne(Builder $query, mixed $value, string $column): void;

    abstract protected function applyMulti(Builder $query, array $values, string $column): void;

    public function __invoke(Builder $query, $value, string $property): void
    {
        if ($this->addRelationConstraint) {
            if ($this->isRelationProperty($query, $property)) {
                $this->withRelationConstraint($query, $value, $property);

                return;
            }
        }

        $normalizedValues = $this->normalizeValue($value);

        if ($normalizedValues->isEmpty()) {
            return;
        }

        $normalizedValues->count() > 1
            ? $this->applyMulti($query, $normalizedValues->all(), $this->qualifyColumn($query, $property))
            : $this->applyOne($query, $normalizedValues->first(), $this->qualifyColumn($query, $property));
    }

    protected function normalizeValue(mixed $source): Collection
    {
        return collect(is_array($source) ? $source : [$source])
            ->filter($this->filterValue(...))
            ->map($this->castValue(...));
    }

    protected function filterValue(mixed $source): bool
    {
        return filled($source);
    }

    protected function castValue(mixed $source): mixed
    {
        return $source;
    }

    protected function qualifyColumn(Builder $query, string $column): string
    {
        return $query->qualifyColumn($column);
    }
}
