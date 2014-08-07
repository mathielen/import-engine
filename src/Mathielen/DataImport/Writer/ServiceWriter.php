<?php
namespace Mathielen\DataImport\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;
use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;
use Mathielen\DataImport\Writer\ObjectWriter\DefaultObjectFactory;

/**
 * Writes data to a given service
 */
class ServiceWriter implements WriterInterface
{

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable, $classOrObjectFactory=null)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("Cannot call callable");
        }
        $this->callable = $callable;

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

    private function write($objectOrItem)
    {
        return call_user_func_array($this->callable, array($objectOrItem));
    }

    /**
     * {@inheritDoc}
     */
    public function finish()
    {
        return $this;
    }

}
