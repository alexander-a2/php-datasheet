<?php

namespace AlexanderA2\PhpDatasheet\DataReader;

use AlexanderA2\PhpDatasheet\Datasheet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderDataReader extends AbstractDataReader implements DataReaderInterface
{
    public function readData(): ArrayCollection
    {
        $result = $this->getSource()->getQuery()->getResult();

        return new ArrayCollection($result);
    }

    public static function supports(Datasheet $datasheet): bool
    {
        return $datasheet->getSource() instanceof QueryBuilder;
    }

    public function getTotalRecords(): int
    {
        return 1;
    }
}