<?php

namespace Mathielen\DataImport\Writer\ObjectWriter;

use JMS\Serializer\SerializerInterface;

class JmsSerializerObjectFactory implements ObjectFactoryInterface
{
    private $classname;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $cache = [];

    public function __construct($classname, SerializerInterface $serializer)
    {
        $this->classname = $classname;
        $this->serializer = $serializer;
    }

    public function factor(array $item)
    {
        //lowercase properties because the naming-strategy of jms serializer depends on lowercase'd prop's
        $item = array_change_key_case($item, CASE_LOWER);
        $json = json_encode($item);

        $cacheKey = md5($json);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $object = $this->serializer->deserialize($json, $this->classname, 'json');
        $this->cache[$cacheKey] = $object;

        if (count($this->cache) > 100) {
            array_shift($this->cache); //cut last one
        }

        return $object;
    }
}
