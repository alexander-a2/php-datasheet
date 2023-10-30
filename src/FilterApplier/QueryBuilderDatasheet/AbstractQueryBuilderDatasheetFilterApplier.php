<?php

namespace AlexanderA2\PhpDatasheet\FilterApplier\QueryBuilderDatasheet;

use AlexanderA2\PhpDatasheet\DataReader\QueryBuilderDataReader;
use AlexanderA2\PhpDatasheet\Filter\FilterInterface;
use AlexanderA2\PhpDatasheet\FilterApplier\FilterApplierContext;
use AlexanderA2\PhpDatasheet\FilterApplier\FilterApplierInterface;

abstract class AbstractQueryBuilderDatasheetFilterApplier implements FilterApplierInterface
{
    public const SUPPORTED_FILTER_CLASS = FilterInterface::class;

    public function supports(FilterApplierContext $context): bool
    {
        return $context->getDataReader() instanceof QueryBuilderDataReader
            && get_class($context->getFilter()) === static::SUPPORTED_FILTER_CLASS;
    }
}