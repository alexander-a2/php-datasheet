<?php

namespace AlexanderA2\PhpDatasheet;

use AlexanderA2\PhpDatasheet\Helper\ObjectHelper;
use AlexanderA2\PhpDatasheet\Helper\StringHelper;

class DatasheetColumn implements DatasheetColumnInterface
{
    protected string $title;

    protected mixed $handler = null;

    public function __construct(
        protected string $name,
        protected ?string $dataType,
    ) {
        $this->title = StringHelper::toReadable($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getHandler(): mixed
    {
        return $this->handler;
    }

    public function setHandler($handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function getContent(mixed $record): string
    {
        $value = ObjectHelper::getProperty($record, $this->name) ?: null;

        if ($this->handler) {
            return call_user_func($this->handler, $value, $record, $this);
        }

        if (empty($value)) {
            return '';
        }

        return call_user_func_array([$this->getDataType(), 'toString'], [$value]);
    }
}