<?php

namespace Mathielen\ImportEngine\Storage\Format;

class ExcelFormat extends Format
{
    private $headerinfirstrow = true;
    private $activesheet = null;
    private $exceltype = 'Excel2007';

    protected $name = 'Excel File';
    protected $id = 'excel';

    public function __construct($headerinfirstrow = true)
    {
        $this->headerinfirstrow = $headerinfirstrow;
    }

    public function isHeaderInFirstRow()
    {
        return $this->headerinfirstrow;
    }

    public function getActivesheet()
    {
        return $this->activesheet;
    }

    public function getExceltype()
    {
        return $this->exceltype;
    }
}
