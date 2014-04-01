<?php
namespace Mathielen\ImportEngine\Storage\Type;

class CsvType extends Type
{

    public $delimiter = ';';
    public $enclosure = '"';
    public $escape = '\\';
    public $headerinfirstrow = true;

    protected $id = 'csv';
    protected $name = 'CSV File';

    public function __construct($delimiter = ';', $enclosure = '"', $escape = '\\', $headerinfirstrow = true)
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->headerinfirstrow = $headerinfirstrow;
    }

    public function __toString()
    {
        return parent::__toString() . ' (Separator = '.$this->delimiter.')';
    }

}
