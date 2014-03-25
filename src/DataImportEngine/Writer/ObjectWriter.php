<?php
namespace DataImportEngine\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;

class ObjectWriter implements WriterInterface
{

    private $objectHandler;
    private $objectFactory;
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function setObjectFactory($objectFactory)
    {

    }

    public function setObjectHandler($objectHandler)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        return $this;
    }

    private function factorObject(array $item)
    {
        if ($this->objectFactory) {
            $object = $this->objectFactory->factor($item);
        } else {
            $class = $this->class;
            $object = new $class();
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function writeItem(array $item)
    {
        $object = $this->factorObject($item);

        if ($this->objectHandler) {
            $this->objectHandler->handle($object);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function finish()
    {
        return $this;
    }
}
