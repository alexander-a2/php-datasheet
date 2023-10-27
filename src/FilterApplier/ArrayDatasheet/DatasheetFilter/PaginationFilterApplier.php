<?php

namespace AlexanderA2\PhpDatasheet\FilterApplier\ArrayDatasheet\DatasheetFilter;

use AlexanderA2\PhpDatasheet\Filter\PaginationFilter;
use AlexanderA2\PhpDatasheet\FilterApplier\ArrayDatasheet\AbstractArrayDatasheetFilterApplier;
use AlexanderA2\PhpDatasheet\FilterApplier\FilterApplierContext;

class PaginationFilterApplier extends AbstractArrayDatasheetFilterApplier
{
    public const SUPPORTED_FILTER_CLASS = PaginationFilter::class;

    public function apply(FilterApplierContext $context)
    {
        $parameters = $context->getFilter()->getParameters();
        $context->getDataReader()->setSource(
            array_slice(
                $context->getDataReader()->getSource(),
                $parameters['recordsPerPage'] * ($parameters['currentPage'] - 1),
                $parameters['recordsPerPage'],
            )
        );
    }
}