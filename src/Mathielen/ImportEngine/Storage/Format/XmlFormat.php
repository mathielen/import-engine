<?php
namespace Mathielen\ImportEngine\Storage\Format;

class XmlFormat extends Format
{

    private $xpath;

    protected $name = 'XML File';
    protected $id = 'xml';

    public function __construct($xpath=null)
    {
        $this->xpath = $xpath;
    }

    public function getXpath()
    {
        return $this->xpath;
    }

}
