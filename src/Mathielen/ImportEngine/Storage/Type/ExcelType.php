<?php
namespace Mathielen\ImportEngine\Storage\Type;

class ExcelType extends Type
{

    public $headerinfirstrow = true;

    protected $name = 'Excel File';
    protected $id = 'excel';

    public function __construct($headerinfirstrow = true)
    {
        $this->headerinfirstrow = $headerinfirstrow;
    }

}
