<?php
namespace Mathielen\DataImport\ValueConverter;

class ExcelGenericDateItemConverter extends GenericDateItemConverter
{

    public function convert($input)
    {
        if (!$input) {
            return null;
        }

        if (is_numeric($input)) {
            $date = \PHPExcel_Shared_Date::ExcelToPHPObject($input);

            return $this->formatOutput($date);
        }

        return parent::convert($input);
    }

}
