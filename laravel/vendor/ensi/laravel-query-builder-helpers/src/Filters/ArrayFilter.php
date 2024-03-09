<?php

namespace Ensi\QueryBuilderHelpers\Filters;

class ArrayFilter extends Filter
{
    public function exact(): static
    {
        return $this->addFilter(ExtraFilter::arrayExact(...$this->makeFilterParams(self::EQUAL)));
    }

    public function contain(): static
    {
        return $this->addFilter(ExtraFilter::arrayContain(...$this->makeFilterParams(self::CONTAIN)));
    }
}
