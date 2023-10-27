<?php

namespace AlexanderA2\PhpDatasheet\FilterApplier;

use AlexanderA2\PhpDatasheet\DataReader\DataReaderInterface;
use AlexanderA2\PhpDatasheet\DatasheetColumnInterface;
use AlexanderA2\PhpDatasheet\Filter\FilterInterface;

class FilterApplierContext
{
    public function __construct(
        protected DataReaderInterface       $dataReader,
        protected FilterInterface           $filter,
        protected ?DatasheetColumnInterface $datasheetColumn = null,
    ) {
    }

    public function getDataReader(): DataReaderInterface
    {
        return $this->dataReader;
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    public function getDatasheetColumn(): ?DatasheetColumnInterface
    {
        return $this->datasheetColumn;
    }
}