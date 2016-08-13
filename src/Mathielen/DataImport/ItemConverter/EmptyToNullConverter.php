<?php

namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class EmptyToNullConverter implements ItemConverterInterface
{
    /**
     * Convert an input.
     *
     * @param mixed $input Input
     *
     * @return array|null the modified input or null to remove it
     */
    public function convert($input)
    {
        return array_filter($input, function ($col) {
            return !is_scalar($col) || trim($col) !== ''; /* dont use empty() - would convert "0" */
        });
    }
}
