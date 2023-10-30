<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\Helper\QueryBuilderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDataReader extends AbstractDataReader implements DataReaderInterface
{
    protected QueryBuilder $originalQueryBuilder;

    public function setSource(mixed $source): self
    {
        $this->source = $source;
        $this->originalQueryBuilder = clone $source;

        return $this;
    }

    public function readData(): ArrayCollection
    {
        $result = $this->getSource()->getQuery()->getArrayResult();

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