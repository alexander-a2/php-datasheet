<?php

namespace AlexanderA2\PhpDatasheet\FilterApplier;

interface FilterApplierInterface
{
    public function supports(FilterApplierContext $context): bool;

    public function apply(FilterApplierContext $context);
}