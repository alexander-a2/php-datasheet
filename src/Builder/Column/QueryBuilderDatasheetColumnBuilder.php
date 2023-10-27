<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DatasheetColumn;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\DataType\BooleanDataType;
use AlexanderA2\PhpDatasheet\DataType\DataTypeInterface;
use AlexanderA2\PhpDatasheet\DataType\DateDataType;
use AlexanderA2\PhpDatasheet\DataType\DateTimeDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\DataType\StringDataType;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder;
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        $columns = new ArrayCollection();
        $selects = $this->getSelectsFromQueryBuilder($datasheet->getSource());

        foreach ($selects as $fieldName => $fieldType) {
            $columns->add(new DatasheetColumn($this->resolveDataType($fieldType)));
        }

        return $datasheet;
    }

    protected function getSelectsFromQueryBuilder(QueryBuilder $queryBuilder)
    {
        $selects = $queryBuilder->getDQLPart('select');

        /** Simple queryBuilder with 'select alias' case */
        if (count($selects) === 1) {
            /** @var Select $firstPart */
            $firstPart = reset($selects);
            $parts = $firstPart->getParts();

            if (count($parts) === 1) {
                $part = reset($parts);

                if ($part === QueryBuilderHelper::getPrimaryAlias($queryBuilder)) {
                    return EntityHelper::getEntityFields(
                        QueryBuilderHelper::getPrimaryClass($queryBuilder),
                        $queryBuilder->getEntityManager(),
                    );
                }
            }
        }
    }

    protected function resolveDataType($fieldType): DataTypeInterface
    {
        $className = match ($fieldType) {
            'string',
            'text',
            'guid' => StringDataType::class,
            'smallint',
            'integer',
            'bigint',
            'decimal',
            'float' => NumericDataType::class,
            'datetime',
            'datetimetz',
            'date_immutable' => DateTimeDataType::class,
            'date' => DateDataType::class,
            'boolean' => BooleanDataType::class,
            default => ObjectDataType::class,
        };

        return new $className;
    }
}