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
     * @var array
     */
    private $arguments;

    private $objectTransformer;
    private $objectFactory;

    public function __construct(callable $callable, $arguments=array(), $objectMapper=null)
    {
        $this->callable = $callable;
        $this->arguments = $arguments;
        $this->setObjectFactory($objectMapper);
        $this->setObjectTransformer($objectMapper);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("Given callable is not a callable");
        }
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
            $this->arguments,
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
