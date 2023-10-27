<?php

namespace AlexanderA2\PhpDatasheet\Helper;

use Throwable;

class ObjectHelper
{
    public static function getProperty(mixed $object, string $propertyName, $throwException = false)
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
            } else {
                return null;
            }
        }
    }
}