<?php

namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class FieldCaseConverter implements ItemConverterInterface
{

    private $transformToCase;

    public function __construct($transformToCase = CASE_LOWER)
    {
        $this->transformToCase = $transformToCase;
    }

    public function convert($input)
    {
        return array_change_key_case($input, $this->transformToCase);
    }
}
