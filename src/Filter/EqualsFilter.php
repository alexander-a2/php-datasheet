<?php

namespace AlexanderA2\PhpDatasheet\Filter;

use AlexanderA2\PhpDatasheet\DataType\StringDataType;

class EqualsFilter extends AbstractFilter
{
    public const SHORT_NAME = 'eq';

    protected array $attributes = [
        'value' => StringDataType::class,
    ];
}