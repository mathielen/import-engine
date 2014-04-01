<?php
namespace Mathielen\DataImport\Writer\ObjectWriter;

use JMS\Serializer\Serializer;

class JmsSerializerObjectFactory implements ObjectFactoryInterface
{

    private $classname;

    public function __construct($classname, Serializer $serializer)
    {
        $this->classname = $classname;
        $this->serializer = $serializer;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    public function factor(array $item)
    {
        $json = json_encode($item);
        $object = $this->serializer->deserialize($json, $this->classname, 'json');

        return $object;
    }
}
