<?php

namespace AlexanderA2\PhpDatasheet\Filter;

use AlexanderA2\PhpDatasheet\DataType\StringDataType;

class ContainsFilter extends AbstractFilter
{
    public const SHORT_NAME = 'has';

    protected array $attributes = [
        'value' => StringDataType::class,
    ];
}