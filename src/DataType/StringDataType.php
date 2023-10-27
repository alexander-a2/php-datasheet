<?php
declare(strict_types=1);

namespace AlexanderA2\PhpDatasheet\DataType;

use AlexanderA2\PhpDatasheet\Filter\ContainsFilter;
use AlexanderA2\PhpDatasheet\Filter\EqualsFilter;

class StringDataType implements DataTypeInterface
{
    public static function toString($value): string
    {
        return (string) $value;
    }

    public static function fromString($value): string
    {
        return (string) $value;
    }

    public static function getFilters(): array
    {
        return [
            EqualsFilter::class,
            ContainsFilter::class,
        ];
    }

    public static function is(mixed $value): bool
    {
        return is_string($value);
    }
}