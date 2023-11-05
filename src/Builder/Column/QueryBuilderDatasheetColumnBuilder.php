<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DatasheetColumn;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        }

        return $datasheet;
    }

    protected function addAllEntityFields(
        DatasheetInterface     $datasheet,
        string                 $entityClassName,
        EntityManagerInterface $entityManager,
    ): void {
        foreach (EntityHelper::get($entityManager)->getEntityFields($entityClassName) as $fieldName => $fieldType) {
            $datasheet->addColumn(new DatasheetColumn($fieldName, EntityHelper::resolveDataTypeByFieldType($fieldType)));
        }
    }

    protected function getSelectsFromQueryBuilder(QueryBuilder $queryBuilder): array
    {
        $selects = $queryBuilder->getDQLPart('select');
        $allEntityFields = EntityHelper::get($queryBuilder->getEntityManager())
            ->getEntityFields(QueryBuilderHelper::getPrimaryClass($queryBuilder));
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
}