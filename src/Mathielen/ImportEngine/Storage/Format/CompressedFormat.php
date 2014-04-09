<?php
namespace Mathielen\ImportEngine\Storage\Format;

class CompressedFormat extends Format
{

    /**
     * @var Format
     */
    private $subFormat;

    protected $name = 'Compressed File';
    protected $id = 'zlib';

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
