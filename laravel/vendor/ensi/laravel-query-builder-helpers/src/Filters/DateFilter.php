<?php

namespace Ensi\QueryBuilderHelpers\Filters;

class DateFilter extends Filter
{
    public function exact(): static
    {
        return $this->addFilter(ExtraFilter::dateExact(...$this->makeFilterParams(self::EQUAL)));
    }

    public function empty(): static
    {
        return $this->addFilter(ExtraFilter::empty(...$this->makeFilterParams(self::EMPTY)));
    }

    public function not(): static
    {
        return $this->addFilter(ExtraFilter::dateNot(...$this->makeFilterParams(self::NOT)));
    }

    public function gt(): static
    {
        return $this->addFilter(ExtraFilter::dateGreater(...$this->makeFilterParams(self::GREATER)));
    }

    public function gte(): static
    {
        return $this->addFilter(ExtraFilter::dateGreaterOrEqual(...$this->makeFilterParams(self::GREATER_OR_EQUAL)));
    }

    public function lt(): static
    {
        return $this->addFilter(ExtraFilter::dateLess(...$this->makeFilterParams(self::LESS)));
    }

    public function lte(): static
    {
        return $this->addFilter(ExtraFilter::dateLessOrEqual(...$this->makeFilterParams(self::LESS_OR_EQUAL)));
    }
}
