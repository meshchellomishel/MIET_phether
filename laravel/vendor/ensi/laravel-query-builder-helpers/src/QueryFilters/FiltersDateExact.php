<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Carbon\CarbonImmutable;
use Ensi\QueryBuilderHelpers\Utils\Date;
use Illuminate\Database\Eloquent\Builder;

class FiltersDateExact extends FiltersBase
{
    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $query->whereBetween($column, $this->range($value));
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        $query->where(function (Builder $query) use ($values, $column) {
            foreach ($values as $value) {
                $query->orWhereBetween($column, $this->range($value));
            }
        });
    }

    protected function castValue(mixed $source): CarbonImmutable
    {
        return Date::makeImmutable($source);
    }

    private function range(CarbonImmutable $value): array
    {
        return [$value->startOfDay(), $value->endOfDay()];
    }
}
