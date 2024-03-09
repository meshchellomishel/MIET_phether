<?php

namespace Ensi\QueryBuilderHelpers\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class StringFilter extends Filter
{
    public function exact(): static
    {
        return $this->addFilter(AllowedFilter::exact(...$this->makeFilterParams(self::EQUAL)));
    }

    public function empty(): static
    {
        return $this->addFilter(ExtraFilter::empty(...$this->makeFilterParams(self::EMPTY)));
    }

    public function contain(): static
    {
        return $this->addFilter(ExtraFilter::contain(...$this->makeFilterParams(self::CONTAIN)));
    }

    public function startWith(): static
    {
        return $this->addFilter(ExtraFilter::startWith(...$this->makeFilterParams(self::START_WITH)));
    }

    public function endWith(): static
    {
        return $this->addFilter(ExtraFilter::endWith(...$this->makeFilterParams(self::END_WITH)));
    }
}
