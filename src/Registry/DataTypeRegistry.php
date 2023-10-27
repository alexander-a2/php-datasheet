<?php

namespace AlexanderA2\PhpDatasheet\Registry;

use AlexanderA2\PhpDatasheet\DataType\BooleanDataType;
use AlexanderA2\PhpDatasheet\DataType\DateDataType;
use AlexanderA2\PhpDatasheet\DataType\DateTimeDataType;
use AlexanderA2\PhpDatasheet\DataType\FloatDataType;
use AlexanderA2\PhpDatasheet\DataType\IntegerDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\DataType\StringDataType;

class DataTypeRegistry
{
    public function get(): array
    {
        return [
            BooleanDataType::class,
            DateDataType::class,
            DateTimeDataType::class,
            FloatDataType::class,
            IntegerDataType::class,
            ObjectDataType::class,
            StringDataType::class,
        ];
    }
}