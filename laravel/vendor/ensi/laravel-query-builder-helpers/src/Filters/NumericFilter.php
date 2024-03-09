<?php

namespace Ensi\QueryBuilderHelpers\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class NumericFilter extends Filter
{
    public function exact(): static
    {
        return $this->addFilter(AllowedFilter::exact(...$this->makeFilterParams(self::EQUAL)));
    }

    public function empty(): static
    {
        return $this->addFilter(ExtraFilter::empty(...$this->makeFilterParams(self::EMPTY)));
    }

    public function gt(): static
    {
        return $this->addFilter(ExtraFilter::greater(...$this->makeFilterParams(self::GREATER)));
    }

    public function gte(): static
    {
        return $this->addFilter(ExtraFilter::greaterOrEqual(...$this->makeFilterParams(self::GREATER_OR_EQUAL)));
    }

    public function lt(): static
    {
        return $this->addFilter(ExtraFilter::less(...$this->makeFilterParams(self::LESS)));
    }

    public function lte(): static
    {
        return $this->addFilter(ExtraFilter::lessOrEqual(...$this->makeFilterParams(self::LESS_OR_EQUAL)));
    }
}
