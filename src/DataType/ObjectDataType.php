<?php
declare(strict_types=1);

namespace AlexanderA2\PhpDatasheet\DataType;

use DateTimeInterface;
use Exception;

class ObjectDataType implements DataTypeInterface
{
    private const LIMIT = 20;

    public static function toString($value): string
    {
        if (is_scalar($value)) {
            return self::prepare((string)$value);
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_COOKIE);
        }

        return 'object';
    }

    public static function fromString($value): string
    {
        throw new Exception();
    }

    public static function prepare(string $value): string
    {
        return mb_substr($value, 0, self::LIMIT);
    }

    public static function getFilters(): array
    {
        return [];
    }


    public static function is(mixed $value): bool
    {
        return is_object($value);
    }
}