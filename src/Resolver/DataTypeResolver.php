<?php

namespace AlexanderA2\PhpDatasheet\Resolver;

use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;

class DataTypeResolver
{
    public function __construct(
        protected array $dataTypes
    ) {
    }

    public function guess($value): string
    {
        foreach ($this->dataTypes as $dataType) {
            if (call_user_func_array([$dataType, 'is'], [$value])) {
                return $dataType;
            }
        }

        return ObjectDataType::class;
    }
}