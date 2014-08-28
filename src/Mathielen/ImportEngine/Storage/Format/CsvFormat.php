<?php
namespace Mathielen\ImportEngine\Storage\Format;

class CsvFormat extends Format
{

    private $delimiter = ';';
    private $enclosure = '"';
    private $escape = '\\';
    private $headerinfirstrow = true;

    protected $id = 'csv';
    protected $name = 'CSV File';

    public function __construct($delimiter = ';', $enclosure = '"', $escape = '\\', $headerinfirstrow = true)
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->headerinfirstrow = $headerinfirstrow;
    }

    public function isHeaderInFirstRow()
    {
        return $this->headerinfirstrow;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function getEnclosure()
    {
        return $this->enclosure;
    }

    public function getEscape()
    {
        return $this->escape;
    }

    public function __toString()
    {
        return parent::__toString() . ' (Separator = '.$this->delimiter.')';
    }

}
