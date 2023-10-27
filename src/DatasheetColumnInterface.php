<?php

namespace AlexanderA2\PhpDatasheet;

interface DatasheetColumnInterface
{
    public function __construct(
        string $name,
        string $dataType,
    );

    public function getName(): string;

    public function getDataType(): string;

    public function getTitle(): string;

    public function setTitle(string $title): self;

    public function getContent(mixed $record): string;
}