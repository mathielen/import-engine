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

    public function __construct(Format $subFormat=null)
    {
        $this->subFormat = $subFormat;
    }

    public function getSubFormat()
    {
        return $this->subFormat;
    }

}
