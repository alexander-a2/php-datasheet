<?php

namespace AlexanderA2\PhpDatasheet\Helper;

use Throwable;

class ObjectHelper
{
    public static function getProperty(mixed $object, string $propertyName, $throwException = false): mixed
    {
        try {
            if (is_array($object)) {
                return $object[$propertyName];
            }

            if (is_object($object)) {
                $getter = 'get' . $propertyName;

                if (method_exists($object, $getter)) {
                    return $object->{$getter}();
                }
            }
        } catch (Throwable $exception) {
            if ($throwException) {
                throw new $exception;
            }
        }

        return null;
    }

    public static function setProperty(mixed $object, string $propertyName, mixed $value, $throwException = false): mixed
    {
        try {
            if (is_array($object)) {
                $object[$propertyName] = $value;
            }

            if (is_object($object)) {
                $setter = 'set' . $propertyName;

                if (method_exists($object, $setter)) {
                    $object->{$setter}($value);
                }
            }
        } catch (Throwable $exception) {
            if ($throwException) {
                throw new $exception;
            }
        }

        return $object;
    }
}