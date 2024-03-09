<?php

namespace Ensi\QueryBuilderHelpers\Utils;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date as IlluminateDate;

class Date
{
    public static function makeImmutable(mixed $value): mixed
    {
        return static::make($value)->toImmutable();
    }

    public static function make(mixed $value): CarbonInterface
    {
        if (is_int($value)) {
            if (config('query-builder-helpers.timestamp_ms', true)) {
                return IlluminateDate::createFromTimestampMs($value);
            } else {
                return IlluminateDate::createFromTimestamp($value);
            }
        }

        return IlluminateDate::parse($value);
    }
}
