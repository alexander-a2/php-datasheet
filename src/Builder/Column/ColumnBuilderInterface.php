<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DatasheetInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool;

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface;
}