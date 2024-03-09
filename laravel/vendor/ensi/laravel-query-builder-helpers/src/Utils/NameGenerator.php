<?php

namespace Ensi\QueryBuilderHelpers\Utils;

class NameGenerator
{
    /** @var array<string, string> */
    private readonly array $suffixes;

    public function __construct(?array $suffixes = null)
    {
        $this->suffixes = $suffixes ?? config('query-builder-helpers.suffixes');
    }

    public function generate(string $name, string $type): string
    {
        $suffix = $this->suffixes[$type] ?? "_{$type}";

        return "{$name}{$suffix}";
    }
}
