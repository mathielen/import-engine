<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\ServiceReader;
use Mathielen\DataImport\Writer\ServiceWriter;

class ServiceStorage implements StorageInterface
{

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

    private $objectTransformer;
    private $objectFactory;

    public function __construct(callable $callable, $objectMapper=null)
    {
        $this->callable = $callable;
        $this->setObjectFactory($objectMapper);
        $this->setObjectTransformer($objectMapper);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("Cannot call callable");
        }
    }

    public function setPrepareCallable(callable $callable)
    {
        $this->prepareCallable = $callable;
    }

    public function setFinishCallable(callable $callable)
    {
        $this->finishCallable = $callable;
    }

    public function setObjectFactory($objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    public function setObjectTransformer($objectTransformer)
    {
        $this->objectTransformer = $objectTransformer;
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        $reader = new ServiceReader(
            $this->callable,
            $this->objectTransformer
        );

        return $reader;
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        $writer = new ServiceWriter(
            $this->callable,
            $this->objectFactory
        );

        if ($this->prepareCallable) {
            $writer->setPrepareCallable($this->prepareCallable);
        }
        if ($this->finishCallable) {
            $writer->setFinishCallable($this->finishCallable);
        }

        return $writer;
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        return new StorageInfo(array(
            'name' => $this->callable.'',
            'format' => 'Service method',
            'size' => 0,
            'count' => count($this->reader())
        ));
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        return $this->reader()->getFields();
    }
}
