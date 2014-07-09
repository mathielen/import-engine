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

    /**
     * @var callable
     */
    private $prepareCallable;

    /**
     * @var callable
     */
    private $finishCallable;

    public function __construct(callable $callable, $classOrObjectFactory=null)
    {
        $this->setObjectFactory($classOrObjectFactory);
        $this->callable = $callable;

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("Cannot call callable");
        }
    }

    public function setObjectFactory($classOrObjectFactory)
    {
        if (is_object($classOrObjectFactory) && $classOrObjectFactory instanceof ObjectFactoryInterface) {
            $objectFactory = $classOrObjectFactory;
        } elseif (is_string($classOrObjectFactory)) {
            $objectFactory = new DefaultObjectFactory($classOrObjectFactory);
        }

        $this->objectFactory = $objectFactory;
    }

    public function setPrepareCallable(callable $callable)
    {
        $this->prepareCallable = $callable;
    }

    public function setFinishCallable(callable $callable)
    {
        $this->finishCallable = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        if ($this->prepareCallable) {
            call_user_func_array($this->prepareCallable, array());
        }

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
        if ($this->finishCallable) {
            call_user_func_array($this->finishCallable, array());
        }

        return $this;
    }

}
