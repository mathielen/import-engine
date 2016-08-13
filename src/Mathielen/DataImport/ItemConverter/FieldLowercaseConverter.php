<?php

namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class FieldLowercaseConverter implements ItemConverterInterface
{
    public function convert($input)
    {
        return array_change_key_case($input, CASE_LOWER);
    }
}
