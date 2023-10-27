<?php

namespace AlexanderA2\PhpDatasheet\Filter;

use AlexanderA2\PhpDatasheet\DataType\IntegerDataType;

class PaginationFilter extends AbstractFilter
{
    public const SHORT_NAME = 'pgn';

    protected array $attributes = [
        'recordsPerPage' => IntegerDataType::class,
        'currentPage' => IntegerDataType::class,
    ];

    protected array $parameters = [
        'recordsPerPage' => 10,
        'currentPage' => 1,
    ];
}