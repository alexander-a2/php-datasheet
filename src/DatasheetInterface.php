<?php

namespace AlexanderA2\PhpDatasheet;

use AlexanderA2\PhpDatasheet\DataReader\DataReaderInterface;

interface DatasheetInterface
{
    public function getSource(): mixed;

    public function getName(): string;

    public function getDataReader(): DataReaderInterface;

    public function setDataReader(DataReaderInterface $dataReader): void;

    public function getTotalRecordsUnfiltered(): int;

    public function setTotalRecordsUnfiltered(int $totalRecordsUnfiltered): self;

    public function getTotalRecordsFiltered(): int;

    public function setTotalRecordsFiltered(int $totalRecordsFiltered): self;

    public function addColumn(DatasheetColumn $column): self;

    public function getColumn(string $name): DatasheetColumnInterface;

    public function getColumns(): array;

    /**
     * @return DatasheetColumnCustomized[]
     */
    public function getCustomizedColumns(): array;

    public function setBuilt(): self;
}