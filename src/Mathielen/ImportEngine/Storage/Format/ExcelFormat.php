<?php
namespace Mathielen\ImportEngine\Storage\Format;

class ExcelFormat extends Format
{

    public $headerinfirstrow = true;
    public $activesheet = 0;

    protected $name = 'Excel File';
    protected $id = 'excel';

    public function __construct($headerinfirstrow = true)
    {
        $this->headerinfirstrow = $headerinfirstrow;
    }

}
