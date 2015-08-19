<?php
namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class TrimConverter implements ItemConverterInterface
{

    public function convert($input)
    {
        return is_array($input)?array_map(function ($item) { return trim($item); }, $input):trim($input);
    }

}
