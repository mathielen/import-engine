<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\ServiceReader;

class ServiceStorage implements StorageInterface
{

    private $service;

    private $methodName;

    public function __construct($service, $methodName)
    {
        $this->service = $service;
        $this->methodName = $methodName;

        if (!is_callable(array($service, $methodName))) {
            throw new \InvalidArgumentException("Cannot call method $methodName on service of class ".get_class($service));
        }
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        return new ServiceReader($this->service, $this->methodName);
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        return new ServiceWriter($this->service, $this->methodName);
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        return array(
            'name' => get_class($this->service).'.'.$this->methodName,
            'format' => 'Service method',
            'size' => 0,
            'count' => count($this->reader())
        );
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        return $this->reader()->getFields();
    }
}
