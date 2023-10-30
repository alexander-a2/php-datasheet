<?php

namespace AlexanderA2\PhpDatasheet;

use AlexanderA2\PhpDatasheet\Helper\StringHelper;

class DatasheetColumnCustomized extends DatasheetColumn
{
    protected array $customizedAttributes = [];

    public function setHandler($handler): self
    {
        $this->customizedAttributes[] = 'handler';

        return parent::setHandler($handler);
    }

    public function setTitle(string $title): self
    {
        $this->customizedAttributes[] = 'handler';

        return parent::setTitle($title);
    }

    public function getCustomizedAttributes(): array
    {
        return $this->customizedAttributes;
    }
}