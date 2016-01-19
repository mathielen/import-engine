<?php
namespace Mathielen\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class GenericDateItemConverter extends DateTimeValueConverter
{

    public function __construct($outputFormat = 'Y-m-d')
    {
        parent::__construct(null, $outputFormat);
    }

    public function convert($input)
    {
        if (!$input) {
            return null;
        }

        // dd.mm.yyyy, dd-mm-yyyy
        try {
            if (preg_match('/^([0-9]{1,2})[\.-]?([0-9]{1,2})[\.-]?([0-9]{2,4})$/', $input, $matches)) {
                $date = new \DateTime($matches[3] . '-' . $matches[2] . '-' . $matches[1]);
            } else {
                $date = new \DateTime($input);
            }

            return $this->formatOutput($date);
        } catch (\Exception $e) {
            return $input;
        }
    }

    protected function formatOutput(\DateTime $date)
    {
        return $date->format($this->outputFormat);
    }

}
