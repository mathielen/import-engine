<?php
namespace Mathielen\DataImport\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;
use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;
use Mathielen\DataImport\Writer\ObjectWriter\DefaultObjectFactory;

/**
 * Writes data to a given SplObjectStorage
 */
class ObjectWriter implements WriterInterface
{

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * @var \SplObjectStorage
     */
    private $objectStorage;

    public function __construct(\SplObjectStorage $objectStorage, $classOrObjectFactory=null)
    {
        $this->objectStorage = $objectStorage;

        if (!empty($classOrObjectFactory)) {
            $this->setObjectFactory($classOrObjectFactory);
        }
    }

    public function setObjectFactory($classOrObjectFactory)
    {
        if (empty($classOrObjectFactory)) {
            throw new \InvalidArgumentException("classOrObjectFactory must not be empty");
        }

        if (is_object($classOrObjectFactory) && $classOrObjectFactory instanceof ObjectFactoryInterface) {
            $objectFactory = $classOrObjectFactory;
        } elseif (is_string($classOrObjectFactory)) {
            $objectFactory = new DefaultObjectFactory($classOrObjectFactory);
        }

        $this->objectFactory = $objectFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Ddeboer\DataImport\Writer\WriterInterface::writeItem()
     */
    public function writeItem(array $item)
    {
        //convert
        $objectOrItem = $this->convert($item);

        //write
        $this->write($objectOrItem);

        return $this;
    }

    private function convert(array $item)
    {
        //dont convert if no object factory
        if (!$this->objectFactory) {
            return $item;
        }

        $object = $this->objectFactory->factor($item);

        return $object;
    }

    protected function write($object)
    {
        $this->objectStorage->attach($object);
    }

    /**
     * {@inheritDoc}
     */
    public function finish()
    {
        return $this;
    }
}
