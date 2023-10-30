<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\Datasheet;
use Doctrine\Common\Collections\ArrayCollection;

interface DataReaderInterface
{
    public function setSource(mixed $source);

    public function getSource(): mixed;

    public function readData(): ArrayCollection;

    public function getTotalRecords(): int;

    public static function supports(Datasheet $datasheet): bool;
}