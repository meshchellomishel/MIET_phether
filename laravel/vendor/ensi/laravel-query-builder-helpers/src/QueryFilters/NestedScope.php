<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Str;

class NestedScope implements Scope
{
    /** @var array<Closure> */
    protected array $filters = [];

    protected readonly string $id;

    public function __construct(protected readonly Closure|string $relation)
    {
        $this->id = Str::random();
    }

    public function apply(Builder $builder, Model $model): void
    {
        if (empty($this->filters)) {
            return;
        }

        $callback = fn (Builder $query) => $this->applyFilters($query);

        is_string($this->relation)
            ? $builder->whereHas($this->relation, $callback)
            : ($this->relation)($builder, $callback);
    }

    public function attach(Builder $builder): static
    {
        $builder->withGlobalScope($this->id, $this);

        return $this;
    }

    public function addFilter(Closure $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    protected function applyFilters(Builder $query): void
    {
        foreach ($this->filters as $filter) {
            $filter($query);
        }
    }
}
