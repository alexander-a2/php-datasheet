<?php

namespace AlexanderA2\PhpDatasheet\Helper;

use Doctrine\ORM\QueryBuilder;

class QueryBuilderHelper
{
    public static function getPrimaryAlias(QueryBuilder $queryBuilder): ?string
    {
        return $queryBuilder->getRootAliases()[0] ?? null;
    }

    public static function getPrimaryClass(QueryBuilder $queryBuilder): ?string
    {
        return $queryBuilder->getRootEntities()[0] ?? null;
    }
}