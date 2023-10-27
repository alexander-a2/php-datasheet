<?php
declare(strict_types=1);

namespace AlexanderA2\PhpDatasheet\DataType;

use AlexanderA2\PhpDatasheet\Filter\EqualsFilter;

class IntegerDataType implements DataTypeInterface
{
    public static function toString($value): string
    {
        return (string) $value;
    }

    public static function fromString($value): float
    {
        return (float) $value;
    }

    public static function getFilters(): array
    {
        return [
            EqualsFilter::class,
        ];
    }

    public static function is(mixed $value): bool
    {
        return is_int($value);
    }
}