<?php

namespace Mathielen\ImportEngine\Storage\Format;

class XmlFormat extends Format
{
    private $xpath;

    protected $name = 'XML File';
    protected $id = 'xml';

    protected $rowNodeName;
    protected $rootNodeName;
    protected $encoding;
    protected $version;

    public function __construct($xpath = null, $rowNodeName='node', $rootNodeName = 'root', $encoding = null, $version = '1.0')
    {
        $this->xpath = $xpath;
        $this->rowNodeName = $rowNodeName;
        $this->rootNodeName = $rootNodeName;
        $this->encoding = $encoding;
        $this->version = $version;
    }

    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * @return string
     */
    public function getRowNodeName()
    {
        return $this->rowNodeName;
    }

    /**
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->rootNodeName;
    }

    /**
     * @return null
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
