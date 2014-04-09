<?php
namespace Mathielen\ImportEngine\Storage;

use phpDocumentor\Descriptor\Interfaces\ContainerInterface;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;

class ServiceStorage implements StorageInterface
{

    private $serviceName;

    private $methodName;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    public function __construct($serviceName, $methodName)
    {
        $this->serviceName = $serviceName;
        $this->methodName = $methodName;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        // return $this->container->
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        // TODO: Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        // TODO: Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        // TODO: Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        // TODO: Auto-generated method stub
    }
}
