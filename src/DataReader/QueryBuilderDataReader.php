<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\DatasheetBuildException;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected int $joinCount = 0;

    protected QueryBuilder $originalQueryBuilder;

    public function setSource(mixed $source): self
    {
        $this->source = $source;
        $this->originalQueryBuilder = clone $source;

        return $this;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->source;
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

    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder;
    }
}