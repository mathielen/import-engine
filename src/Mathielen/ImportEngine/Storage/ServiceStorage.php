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

    private $objectMapper;
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
        return new ServiceReader(
            $this->callable,
            $this->objectTransformer
        );
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        return new ServiceWriter(
            $this->callable,
            $this->objectFactory
        );
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        return new StorageInfo(array(
            'name' => get_class($this->service).'::'.$this->methodName,
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
