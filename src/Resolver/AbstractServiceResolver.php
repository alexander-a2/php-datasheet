<?php

namespace AlexanderA2\PhpDatasheet\Resolver;

use AlexanderA2\PhpDatasheet\DatasheetBuildException;

abstract class AbstractServiceResolver
{
    public function __construct(
        protected iterable $services,
    ) {
    }

    public function resolve(mixed $context): mixed
    {
        dd(iterator_to_array($context));
        foreach ($this->services as $service) {
            if ($service->supports($context)) {
                return $service;
            }
        }

        throw new DatasheetBuildException('Failed to resolve supported service via ' . get_class($this));
    }
}