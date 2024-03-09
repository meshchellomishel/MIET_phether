<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Carbon\CarbonImmutable;
use Ensi\QueryBuilderHelpers\Enums\RangeOperator;
use Ensi\QueryBuilderHelpers\Utils\Date;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

class FiltersDateRange extends FiltersRange
{
    protected function castValue(mixed $source): CarbonImmutable
    {
        $value = Date::makeImmutable($source) ?? throw InvalidFilterValue::make($source);

        // > (дата исключается) - сравнение с концом дня
        // >= (дата включается) - сравнение с началом дня
        // < (дата исключается) - сравнение с началом дня
        // <= (дата включается) - сравнение с концом дня
        return $this->operator === RangeOperator::GREATER || $this->operator === RangeOperator::LESS_OR_EQUAL
            ? $value->endOfDay()
            : $value->startOfDay();
    }
}
