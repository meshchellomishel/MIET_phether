<?php

namespace Ensi\QueryBuilderHelpers\Enums;

enum RangeOperator: string
{
    case GREATER = '>';
    case GREATER_OR_EQUAL = '>=';
    case LESS = '<';
    case LESS_OR_EQUAL = '<=';
}
