<?php

namespace AlexanderA2\PhpDatasheet;

use AlexanderA2\PhpDatasheet\DataReader\DataReaderInterface;
use AlexanderA2\PhpDatasheet\Filter\FilterInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Datasheet implements DatasheetInterface
{
    private const QUERY_KEYS = [
        'datasheet_filters' => 'df',
        'column_filters' => 'cf',
    ];

    protected ArrayCollection $data;

    protected array $columnsAfterBuild;

    protected array $columnsBeforeBuild;

    protected DataReaderInterface $dataReader;

    protected int $totalRecordsUnfiltered;

    protected int $totalRecordsFiltered;

    protected array $filters = [];

    protected array $columnFilters = [];

    public function __construct(
        protected mixed   $source,
        protected ?string $id = null,
    ) {
        if (empty($id)) {
            $this->id = $this->buildDatasheetId();
        }
    }

    public function getName(): string
    {
        return sprintf('%s%s', 'ds', $this->id);
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function getData(): ArrayCollection
    {
        return $this->data;
    }

    public function setData(ArrayCollection $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function addColumn(DatasheetColumn $column): self
    {
        $this->columnsAfterBuild[$column->getName()] = $column;

        return $this;
    }

    public function setColumn(DatasheetColumn $column): self
    {
        if (empty($this->columnsAfterBuild)) {
            $this->columnsBeforeBuild[$column->getName()] = $column;
        } else {
            $this->columnsAfterBuild[$column->getName()] = $column;
        }

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columnsAfterBuild;
    }

    public function getColumnsBeforeBuild(): array
    {
        return $this->columnsBeforeBuild;
    }

    public function getDataReader(): DataReaderInterface
    {
        return $this->dataReader;
    }

    public function setDataReader(DataReaderInterface $dataReader): void
    {
        $this->dataReader = $dataReader;
    }

    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getShortName()] = $filter;
        return $this;
    }

    public function getFilter(string $shortName): FilterInterface
    {
        return $this->filters[$shortName];
    }

    /** @return FilterInterface[] */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addColumnFilter(string $columnName, FilterInterface $filter): self
    {
        $this->columnFilters[$columnName][] = $filter;

        return $this;
    }

    /** @return FilterInterface[] */
    public function getColumnFilters(string $columnName): array
    {
        return $this->columnFilters[$columnName] ?? [];
    }

    public function getQueryKey(string $name): string
    {
        return self::QUERY_KEYS[$name];
    }

    public function getTotalRecordsUnfiltered(): int
    {
        return $this->totalRecordsUnfiltered;
    }

    public function setTotalRecordsUnfiltered(int $totalRecordsUnfiltered): self
    {
        $this->totalRecordsUnfiltered = $totalRecordsUnfiltered;
        return $this;
    }

    public function getTotalRecordsFiltered(): int
    {
        return $this->totalRecordsFiltered;
    }

    public function setTotalRecordsFiltered(int $totalRecordsFiltered): self
    {
        $this->totalRecordsFiltered = $totalRecordsFiltered;
        return $this;
    }

    protected function buildDatasheetId(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

        if (count($backtrace) >= 2) {
            return mb_substr(md5($backtrace[1]['file'] . PHP_EOL . $backtrace[1]['line']), 0, 3);
        }

        return mb_substr(md5($this->getSource()), 0, 3);
    }
}