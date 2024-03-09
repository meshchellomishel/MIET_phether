<?php

namespace Ensi\QueryBuilderHelpers\QueryFilters;

use Ensi\QueryBuilderHelpers\Enums\LikeMode;
use Illuminate\Database\Eloquent\Builder;

class FiltersLike extends FiltersBase
{
    public function __construct(protected readonly LikeMode $mode, bool $addRelationConstraint = true)
    {
        parent::__construct($addRelationConstraint);
    }

    protected function applyOne(Builder $query, mixed $value, string $column): void
    {
        $query->whereRaw($this->makeSql($query, $column), [$this->makeTemplate($value)]);
    }

    protected function applyMulti(Builder $query, array $values, string $column): void
    {
        $query->where(function (Builder $query) use ($values, $column) {
            $sql = $this->makeSql($query, $column);

            foreach ($values as $value) {
                $query->orWhereRaw($sql, [$this->makeTemplate($value)]);
            }
        });
    }

    protected function makeSql(Builder $query, string $column): string
    {
        $wrappedProperty = $query->getQuery()->getGrammar()->wrap($column);

        $likeOperator = config('query-builder-helpers.like_operator');

        return "{$wrappedProperty} {$likeOperator} ?";
    }

    protected function makeTemplate(string $source): string
    {
        $value = mb_strtolower($source, 'UTF8');

        return match ($this->mode) {
            LikeMode::START_WITH => "{$value}%",
            LikeMode::END_WITH => "%{$value}",
            LikeMode::CONTAIN => "%{$value}%",
        };
    }
}
