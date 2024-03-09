<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Carbon\CarbonImmutable;
use Ensi\QueryBuilderHelpers\Utils\Date;
use Illuminate\Database\Eloquent\Builder;

class FiltersDateNot extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $this->whereNotWithinDate($query, $value, $column);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        foreach ($values as $value) {
            $this->whereNotWithinDate($query, $value, $column);
        }
    }

    protected function castValue(mixed $source): CarbonImmutable
    {
        return Date::makeImmutable($source);
    }

    protected function whereNotWithinDate(Builder $query, CarbonImmutable $value, string $column): void
    {
        $query->whereNot(function (Builder $query) use ($value, $column) {
            $query->whereDate($column, $value)->whereNotNull($column);
        });
    }
}
