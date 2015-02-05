<?php
namespace Mathielen\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class GenericDateItemConverter extends DateTimeValueConverter
{

    public function __construct($outputFormat)
    {
        parent::__construct(null, $outputFormat);
    }

    public function convert($input)
    {
        if (!$input) {
            return;
        }

        // dd.mm.yyyy, dd-mm-yyyy
        if (preg_match('/^([0-9]{1,2})[\.-]([0-9]{1,2})[\.-]([0-9]{2,4})$/', $input, $matches)) {
            $date = new \DateTime($matches[3].'-'.$matches[2].'-'.$matches[1]);
        } else {
            $date = new \DateTime($input);
        }

        return $this->formatOutput($date);
    }

    protected function formatOutput(\DateTime $date)
    {
        return $date->format($this->outputFormat);
    }

}
