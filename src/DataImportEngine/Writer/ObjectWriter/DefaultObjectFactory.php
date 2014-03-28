<?php
namespace DataImportEngine\Writer\ObjectWriter;

class DefaultObjectFactory implements ObjectFactoryInterface
{

    private $classname;

    public function __construct($classname)
    {
        $this->classname = $classname;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    public function factor(array $item)
    {
        return json_decode(json_encode($item));
    }
}
