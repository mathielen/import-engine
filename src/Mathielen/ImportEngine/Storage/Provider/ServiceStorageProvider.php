<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use phpDocumentor\Descriptor\Interfaces\ContainerInterface;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;

class ServiceStorageProvider implements StorageProviderInterface
{

    private $serviceName;
    private $methodName;

    /**
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
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        //return $this->container->
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        return new StorageSelection(array($this->serviceName, $this->methodName));
    }

}
