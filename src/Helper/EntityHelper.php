<?php

namespace AlexanderA2\PhpDatasheet\Helper;

use AlexanderA2\PhpDatasheet\DataType\BooleanDataType;
use AlexanderA2\PhpDatasheet\DataType\DateDataType;
use AlexanderA2\PhpDatasheet\DataType\DateTimeDataType;
use AlexanderA2\PhpDatasheet\DataType\FloatDataType;
use AlexanderA2\PhpDatasheet\DataType\IntegerDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\DataType\ObjectsDataType;
use AlexanderA2\PhpDatasheet\DataType\StringDataType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ReflectionClass;

class EntityHelper
{
    public const RELATION_FIELD_TYPES = [
        ClassMetadataInfo::MANY_TO_ONE => 'many_to_one',
        ClassMetadataInfo::MANY_TO_MANY => 'many_to_many',
    ];

    public const PRIMARY_FIELD_TYPICAL_NAMES = [
        'name',
        'firstname',
        'firstName',
        'first_name',
        'fullname',
        'full_name',
        'title',
        'email',
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

        foreach ($classMetadata->getAssociationMappings() as $relation) {
            if (array_key_exists($relation['type'], self::RELATION_FIELD_TYPES)) {
                $fields[$relation['fieldName']] = self::RELATION_FIELD_TYPES[$relation['type']];
            }
        }
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

    public static function getRelationClassName(
        string                 $baseEntityClassName,
        string                 $relationFieldName,
        EntityManagerInterface $entityManager
    ): string {
        $relation = self::getEntityMetadata($baseEntityClassName, $entityManager)
            ->getAssociationMapping($relationFieldName);

        return $relation['targetEntity'];
    }

    public static function getEntityMetadata(string $className, EntityManagerInterface $entityManager): ClassMetadata
    {
        if (!array_key_exists($className, self::$entityMetadataCached)) {
            self::$entityMetadataCached[$className] = $entityManager->getClassMetadata($className);
        }

        return self::$entityMetadataCached[$className];
    }

    public static function getEntityPrimaryAttribute(
        string                 $entityClassName,
        EntityManagerInterface $entityManager
    ): ?string {
        $fields = self::getEntityFields($entityClassName, $entityManager);

        foreach (self::PRIMARY_FIELD_TYPICAL_NAMES as $name) {
            if (array_key_exists($name, $fields)) {
                return $name;
            }
        }

        return null;
    }

    public static function resolveDataTypeByFieldType(string $fieldType): string
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
            self::RELATION_FIELD_TYPES[ClassMetadataInfo::MANY_TO_MANY] => ObjectsDataType::class,
            default => ObjectDataType::class,
        };
    }
}