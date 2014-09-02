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

    public function __construct($uriInsideArchive=null, $wrapper='zip', Format $subFormat=null)
    {
        if (!is_string($wrapper)) {
            throw new \InvalidArgumentException("wrapper argument must be a string");
        }
        if (!is_null($uriInsideArchive) && !is_string($uriInsideArchive)) {
            throw new \InvalidArgumentException("uriInsideArchive argument must be a string");
        }

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
        if (is_null($this->uriInsideArchive)) {
            throw new \LogicException("This compressed archive has multiple files in it. Cannot create a single stream.");
        }

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
