<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\Datasheet;
use Doctrine\Common\Collections\ArrayCollection;

class ArrayDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected int $unfilteredRecordsTotal;

    public function readData(): ArrayCollection
    {
        return new ArrayCollection($this->source);
    }

    public function setSource(mixed $source): self
    {
        $this->source = $source;

        if (empty($this->unfilteredRecordsTotal)) {
            $this->unfilteredRecordsTotal = count($source);
        }

        return $this;
    }

    public function getTotalRecords(): int
    {
        return $this->unfilteredRecordsTotal;
    }

    public static function supports(Datasheet $datasheet): bool
    {
        return is_array($datasheet->getSource());
    }
}