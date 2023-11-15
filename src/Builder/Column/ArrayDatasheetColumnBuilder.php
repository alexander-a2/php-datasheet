<?php

namespace AlexanderA2\PhpDatasheet\Builder\Column;

use AlexanderA2\PhpDatasheet\DataReader\ArrayDataReader;
use AlexanderA2\PhpDatasheet\DatasheetColumn;
use AlexanderA2\PhpDatasheet\DatasheetInterface;
use AlexanderA2\PhpDatasheet\DataType\ObjectDataType;
use AlexanderA2\PhpDatasheet\Registry\DataTypeRegistry;
use AlexanderA2\PhpDatasheet\Resolver\DataTypeResolver;

class ArrayDatasheetColumnBuilder implements ColumnBuilderInterface
{
    public static function supports(DatasheetInterface $datasheet): bool
    {
        return $datasheet->getDataReader() instanceof ArrayDataReader;
    }

    public function addColumnsToDatasheet(DatasheetInterface $datasheet): DatasheetInterface
    {
        $dataTypeResolver = new DataTypeResolver(
            (new DataTypeRegistry())->get(),
        );
        $firstRow = $datasheet->getSource()[0];

        foreach ($firstRow as $columnName => $sampleValue) {
            $dataType = empty($sampleValue) ? ObjectDataType::class : $dataTypeResolver->guess($sampleValue);
            $datasheet->addColumn(new DatasheetColumn($columnName, $dataType));
        }

        return $datasheet;
    }
}