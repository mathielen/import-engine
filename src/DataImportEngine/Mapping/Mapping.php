<?php
namespace DataImportEngine\Mapping;

class Mapping
{

    public $from;
    public $to;
    public $converter;

    public function __construct($from, $to=null, $converter=null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->converter = $converter;
    }

}
