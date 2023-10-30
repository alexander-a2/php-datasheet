<?php

namespace AlexanderA2\PhpDatasheet\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;

class EntityHelper
{
    private const PRIMARY_FIELD_TYPICAL_NAMES = [
        'name',
        'firstname',
        'firstName',
        'first_name',
        'fullname',
        'full_name',
        'title',
        'email',
        'id',
    ];

    static array $entityMetadataCached = [];

    static array $entityListCached;

    public static function getEntityFields(string $className, EntityManagerInterface $entityManager): array
    {
        $classMetadata = self::getEntityMetadata($className, $entityManager);
        $fields = [];

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            $fieldMapping = $classMetadata->getFieldMapping($fieldName);
            $fields[$fieldName] = $fieldMapping['type'];
        }

//        foreach ($classMetadata->getAssociationMappings() as $relation) {
//            if ($relation['type'] === ClassMetadataInfo::MANY_TO_ONE) {
//                $fields[$relation['fieldName']] = 'many_to_one';
//            }
//        }
        $sortedFields = [];

        foreach ((new ReflectionClass($className))->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $fields)) {
                continue;
            }
            $sortedFields[$property->getName()] = $fields[$property->getName()];
        }

        return $sortedFields;
    }

    public static function getEntityList(EntityManagerInterface $entityManager): array
    {
        if (empty(self::$entityListCached)) {
            self::$entityListCached = $entityManager
                ->getConfiguration()
                ->getMetadataDriverImpl()
                ->getAllClassNames();
            sort(self::$entityListCached);
        }

        return self::$entityListCached;
    }

    public static function guessPrimaryFieldName(array $fields): ?string
    {
        foreach (self::PRIMARY_FIELD_TYPICAL_NAMES as $name) {
            if (array_key_exists($name, $fields)) {
                return $name;
            }
        }

        return null;
    }

    protected static function getEntityMetadata(string $className, EntityManagerInterface $entityManager): ClassMetadata
    {
        if (!array_key_exists($className, self::$entityMetadataCached)) {
            self::$entityMetadataCached[$className] = $entityManager->getClassMetadata($className);
        }

        return self::$entityMetadataCached[$className];
    }
}