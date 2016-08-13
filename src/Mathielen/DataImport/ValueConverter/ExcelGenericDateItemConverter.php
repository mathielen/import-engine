<?php

namespace Mathielen\DataImport\ValueConverter;

class ExcelGenericDateItemConverter extends GenericDateItemConverter
{
    public function convert($input)
    {
        if (!$input) {
            return;
        }

        if (is_numeric($input) && $input < 100000) { //Date may be 42338 (=> 30.11.2015
            $date = \PHPExcel_Shared_Date::ExcelToPHPObject($input);

            return $this->formatOutput($date);
        }

        return parent::convert($input);
    }
}
