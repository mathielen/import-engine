<?php
namespace Mathielen\ImportEngine\Storage\Format;

class CompressedFormat extends Format
{

    /**
     * @var Format
     */
    private $subFormat;

    private $uriInsideArchive;
    private $wrapper;

    protected $name = 'Compressed File';
    protected $id = 'compress';

    public function __construct($uriInsideArchive, $wrapper='zip', Format $subFormat=null)
    {
        $this->uriInsideArchive = $uriInsideArchive;
        $this->wrapper = $wrapper;
        $this->subFormat = $subFormat;
    }

    public function getSubFormat()
    {
        return $this->subFormat;
    }

    public function getInsideStream(\SplFileInfo $file)
    {
        $streamUri = $this->wrapper . '://' . $file . '#' . $this->uriInsideArchive;
        $uncompressedUri = tempnam('/tmp', 'compressed');
        file_put_contents($uncompressedUri, file_get_contents($streamUri));

        return new \SplFileInfo($uncompressedUri);
    }

    public function __toString()
    {
        return $this->name . ' with sub-format: '.$this->subFormat;
    }

}
