<?php
namespace Mathielen\ImportEngine\Storage\Format;

class ExcelFormat extends Format
{

    public $headerinfirstrow = true;

    protected $name = 'Excel File';
    protected $id = 'excel';

    public function __construct($headerinfirstrow = true)
    {
        $this->headerinfirstrow = $headerinfirstrow;
    }

}
