<?php
namespace Mathielen\ImportEngine\Storage\Format;

class ZipFormat extends Format
{

    /**
     * @var Format
     */
    private $subFormat;

    protected $name = 'Zip File';
    protected $id = 'zip';

    public function __construct($streamUri=null, Format $subFormat=null)
    {
        $this->streamUri = $streamUri;
        $this->subFormat = $subFormat;
    }

    public function getSubFormat()
    {
        return $this->subFormat;
    }

    public function getStreamUri()
    {
        return $this->streamUri;
    }

    public function __toString()
    {
        return $this->name . ' with sub-format: '.$this->subFormat;
    }

}
