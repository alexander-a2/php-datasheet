<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DatasheetColumn;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\DataType\BooleanDataType;
use AlexanderA2\PhpDatasheet\DataType\DataTypeInterface;
use AlexanderA2\PhpDatasheet\DataType\DateDataType;
use AlexanderA2\PhpDatasheet\DataType\DateTimeDataType;
use AlexanderA2\PhpDatasheet\DataType\FloatDataType;
use AlexanderA2\PhpDatasheet\DataType\IntegerDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectsDataType;
use AlexanderA2\PhpDatasheet\DataType\StringDataType;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder || $datasheet->getSource() instanceof ServiceEntityRepository;
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $datasheet->getDataReader()->getSource();
        $queryBuilder->getDQLPart('select');

        foreach ($queryBuilder->getDQLPart('select') as $select) {
            $select = QueryBuilderHelper::parseSelect($select);

            if (empty($select['fieldName'])) {
                if ($select['alias'] === QueryBuilderHelper::getPrimaryAlias($queryBuilder)) {
                    $this->addAllEntityFields(
                        $datasheet,
                        QueryBuilderHelper::getPrimaryClass($queryBuilder),
                        $queryBuilder->getEntityManager(),
                    );
                }
            }
//            dd($select);
        }

//        $datasheet->addColumn(new DatasheetColumn('id', IntegerDataType::class));
//        $datasheet->addColumn(new DatasheetColumn('fullname', StringDataType::class));
//        dd($datasheet->getSource());
//        $selects = $this->getSelectsFromQueryBuilder($datasheet->getSource());
//        exit;
//
//        foreach ($selects as $fieldName => $fieldType) {
//            $datasheet->addColumn(new DatasheetColumn($fieldName, $this->resolveDataType($fieldType)));
//        }

        return $datasheet;
    }

    protected function addAllEntityFields(
        DatasheetInterface     $datasheet,
        string                 $entityClassName,
        EntityManagerInterface $entityManager,
    ): void {

        foreach (EntityHelper::getEntityFields($entityClassName, $entityManager) as $fieldName => $fieldType) {
            $datasheet->addColumn(new DatasheetColumn($fieldName, $this->resolveDataType($fieldType)));
        }
    }

    protected function getSelectsFromQueryBuilder(QueryBuilder $queryBuilder): array
    {
        $selects = $queryBuilder->getDQLPart('select');
        $allEntityFields = EntityHelper::getEntityFields(
            QueryBuilderHelper::getPrimaryClass($queryBuilder),
            $queryBuilder->getEntityManager(),
        );
        $selectedFields = [];

        foreach ($selects as $select) {
            $select = QueryBuilderHelper::parseSelect($select);
            $fieldName = $select['as'] ?? $select['fieldName'] ?? null;
            if ($fieldName) {
                $selectedFields[$fieldName] = $allEntityFields[$fieldName];
            }
        }

        return $selectedFields;
    }

    protected function resolveDataType($fieldType): string
    {
        return match ($fieldType) {
            'string',
            'text',
            'guid' => StringDataType::class,
            'smallint',
            'integer',
            'bigint' => IntegerDataType::class,
            'decimal',
            'float' => FloatDataType::class,
            'datetime',
            'datetimetz',
            'date_immutable' => DateTimeDataType::class,
            'date' => DateDataType::class,
            'boolean' => BooleanDataType::class,
            EntityHelper::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_MANY] => ObjectsDataType::class,
            default => ObjectDataType::class,
        };
    }
}