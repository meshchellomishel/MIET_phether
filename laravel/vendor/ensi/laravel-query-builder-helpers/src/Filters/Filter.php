<?php

namespace Ensi\QueryBuilderHelpers\Filters;

use ArrayIterator;
use Ensi\QueryBuilderHelpers\Utils\NameGenerator;
use IteratorAggregate;
use Spatie\QueryBuilder\AllowedFilter;
use Traversable;

class Filter implements IteratorAggregate
{
    protected const EQUAL = 'equal';
    protected const GREATER = 'greater';
    protected const GREATER_OR_EQUAL = 'greater_or_equal';
    protected const LESS = 'less';
    protected const LESS_OR_EQUAL = 'less_or_equal';
    protected const CONTAIN = 'contain';
    protected const START_WITH = 'start_with';
    protected const END_WITH = 'end_with';
    protected const EMPTY = 'empty';
    protected const NOT = 'not';

    private ?NameGenerator $nameGenerator;

    /** @var array<AllowedFilter> */
    protected array $filters = [];

    final public function __construct(
        protected string $baseName,
        protected ?string $internalName = null,
    ) {
    }

    public static function make(string $baseName, ?string $internalName = null): static
    {
        return new static($baseName, $internalName);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->filters);
    }

    protected function addFilter(AllowedFilter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @return array{string, string}
     */
    protected function makeFilterParams(string $type): array
    {
        return [static::makeName($type), $this->internalName ?? $this->baseName];
    }

    protected function makeName(string $type): string
    {
        $this->nameGenerator ??= new NameGenerator();

        return $this->nameGenerator->generate($this->baseName, $type);
    }
}
