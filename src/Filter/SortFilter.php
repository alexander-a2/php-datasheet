<?php

namespace AlexanderA2\PhpDatasheet\Filter;

use AlexanderA2\PhpDatasheet\DataType\IntegerDataType;

class SortFilter extends AbstractFilter
{
    public const SHORT_NAME = 'sort';

    protected array $attributes = [
        'by' => IntegerDataType::class,
        'direction' => IntegerDataType::class,
    ];
}