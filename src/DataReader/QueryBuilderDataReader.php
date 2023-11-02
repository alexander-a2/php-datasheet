<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\DatasheetBuildException;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected int $joinCount = 0;

    protected QueryBuilder $originalQueryBuilder;

    public function setSource(mixed $source): self
    {
        $this->source = $source;
        $this->originalQueryBuilder = clone $source;
        $this->addRelations();

        return $this;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->source;
    }

//    protected function handleSelects(): void
//    {
//        // If there is just alias in select part - enrich it, with joining all relation tables.
//        $selects = $this->getQueryBuilder()->getDQLPart('select');
//
//        foreach ($selects as $select) {
//            $select = QueryBuilderHelper::parseSelect($select);
//
//            if (!empty($select[1])) {
//                continue;
//            }
//            // todo: get class from primary or joins
//            $this->addAliasSelects($select['alias'], QueryBuilderHelper::getPrimaryClass($this->getQueryBuilder()));
//        }
//
//        $a = array_map(function ($select) {
//            return QueryBuilderHelper::parseSelect($select);
//        }, $this->getQueryBuilder()->getDQLPart('select'));

//        dd($a);
//        return;

//    }

    /**
     * todo
     * check if relations are already added
     * check if selections are already added
     */
    protected function addRelations(): void
    {
        $primaryAlias = QueryBuilderHelper::getPrimaryAlias($this->getQueryBuilder());
        $entityFields = EntityHelper::getEntityFields(
            QueryBuilderHelper::getPrimaryClass($this->getQueryBuilder()),
            $this->getQueryBuilder()->getEntityManager(),
        );

        foreach ($entityFields as $fieldName => $fieldType) {
            if (!in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                continue;
            }
            $joinAlias = $this->addJoin($primaryAlias, $fieldName);
            $this->getQueryBuilder()->addSelect($joinAlias);
        }
    }

    function aaa()
    {

        $primaryAlias = QueryBuilderHelper::getPrimaryAlias($this->getQueryBuilder());
        $entityFields = EntityHelper::getEntityFields(
            QueryBuilderHelper::getPrimaryClass($this->getQueryBuilder()),
            $this->getQueryBuilder()->getEntityManager(),
        );

        foreach ($entityFields as $fieldName => $fieldType) {
            if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                $joinAlias = $this->joinRelation($primaryAlias, $fieldName);
                $this->getQueryBuilder()->addSelect($joinAlias);
            } else {
                $this->getQueryBuilder()->addSelect(sprintf('%s.%s', $primaryAlias, $fieldName));
            }
        }

        return;

        $selects = $this->getQueryBuilder()->getDQLPart('select');

        /** Simple queryBuilder with 'select alias' case */
        if (count($selects) !== 1) {
            return;
        }

        /** @var Select $firstPart */
        $firstPart = reset($selects);
        $parts = $firstPart->getParts();

        if (count($parts) !== 1) {
            return;
        }
        $part = reset($parts);
        $primaryAlias = QueryBuilderHelper::getPrimaryAlias($this->getQueryBuilder());

        if ($part !== $primaryAlias) {
            return;
        }
        $this->getQueryBuilder()->resetDQLPart('select');
        $entityFields = EntityHelper::getEntityFields(
            QueryBuilderHelper::getPrimaryClass($this->getQueryBuilder()),
            $this->getQueryBuilder()->getEntityManager(),
        );

        foreach ($entityFields as $fieldName => $fieldType) {
            if (in_array($fieldType, EntityHelper::RELATION_FIELD_TYPES)) {
                $joinAlias = $this->joinRelation($primaryAlias, $fieldName);
                $this->getQueryBuilder()->addSelect($joinAlias);
            } else {
                $this->getQueryBuilder()->addSelect(sprintf('%s.%s', $primaryAlias, $fieldName));
            }
        }
    }

    protected function addJoin(string $primaryAlias, string $fieldName): string
    {
        $joins = $this->getQueryBuilder()->getDQLPart('join');
        if (!empty($joins[$primaryAlias])) {
            /** @var Join $join */
            foreach ($joins[$primaryAlias] as $join) {
                if ($join->getJoin() === $primaryAlias . '.' . $fieldName) {
                    throw new DatasheetBuildException('todo');
                }
            }
        }
        ++$this->joinCount;
        $joinAlias = 't' . $this->joinCount;
        $this->getQueryBuilder()->leftJoin($primaryAlias . '.' . $fieldName, $joinAlias);

        return $joinAlias;
    }

    public function readData(): ArrayCollection
    {
//        dd($this->getQueryBuilder());
//        dd($this->getQueryBuilder()->getQuery()->getSQL());
        $result = $this->getQueryBuilder()->getQuery()->getArrayResult();
//        dd($result);

        return new ArrayCollection($result);
    }

    public function getTotalRecords(): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = clone($this->getSource());
        $queryBuilder
            ->resetDQLPart('select')
            ->resetDQLPart('groupBy')
            ->addSelect(sprintf('COUNT(%s.id) AS total', QueryBuilderHelper::getPrimaryAlias($queryBuilder)))
            ->setFirstResult(null)
            ->setMaxResults(null);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public static function supports(Datasheet $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder;
    }
}