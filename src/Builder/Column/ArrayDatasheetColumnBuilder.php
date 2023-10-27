<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DatasheetColumn;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\Registry\DataTypeRegistry;
use AlexanderA2\PhpDatasheet\Resolver\DataTypeResolver;

class ArrayDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return is_array($datasheet->getSource());
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        $dataTypeResolver = new DataTypeResolver(
            (new DataTypeRegistry())->get(),
        );
        $firstRow = $datasheet->getSource()[0];

        foreach ($firstRow as $columnName => $sampleValue) {
            if (array_key_exists($columnName, $datasheet->getColumnsBeforeBuild())) {
                $datasheet->addColumn($datasheet->getColumnsBeforeBuild()[$columnName]);
            } else {
                $dataType = empty($sampleValue) ? ObjectDataType::class : $dataTypeResolver->guess($sampleValue);
                $datasheet->addColumn(new DatasheetColumn($columnName, $dataType));
            }
        }

        return $datasheet;
    }
}