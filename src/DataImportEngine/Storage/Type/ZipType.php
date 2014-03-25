<?php
namespace DataImportEngine\Storage\Type;

class ZipType extends Type
{

    /**
     * @var Type
     */
    private $subType;

    protected $name = 'Zip File';
    protected $id = 'zip';

    public function __construct(Type $subType=null)
    {
        $this->subType = $subType;
    }

    public function getSubType()
    {
        return $this->subType;
    }

}
