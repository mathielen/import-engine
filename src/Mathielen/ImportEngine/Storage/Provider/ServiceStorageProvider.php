<?php

namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\ServiceStorage;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceStorageProvider implements StorageProviderInterface
{
    /**
     * @var array
     *            key = servicename
     *            value = array of methods
     */
    private $services;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, array $services)
    {
        $this->container = $container;
        $this->services = $services;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        $callable = $selection->getImpl();

        $arguments = array();
        if (is_array($callable) && isset($callable['arguments'])) {
            $arguments = $callable['arguments'];
            unset($callable['arguments']);
        } elseif (!is_callable($callable)) {
            throw new \InvalidArgumentException('StorageSelection must contain a callable or an extended callable (with arguments) as impl');
        }

        return new ServiceStorage($callable, $arguments);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        if (!is_array($id) || !isset($id['service']) || !isset($id['method']) || empty($id['service']) || empty($id['method'])) {
            throw new \InvalidArgumentException('Invalid id argument. Must be array and containing at least service and method property.');
        }

        $serviceName = $id['service'];
        if (!isset($this->services[$serviceName])) {
            throw new \InvalidArgumentException("Service '$serviceName' is not registered in StorageProvider.");
        }

        $method = $id['method'];
        if (!empty($this->services[$serviceName]['methods']) && !in_array($method, $this->services[$serviceName]['methods'])) {
            throw new \InvalidArgumentException("Method '$method' is not registered in StorageProvider for service '$serviceName'.");
        }

        $service = $this->container->get($serviceName);
        $callable_with_arguments = array($service, $method);
        if(isset($id['arguments'])){
            $callable_with_arguments['arguments'] = $id['arguments'];
        }

        return new StorageSelection($callable_with_arguments);
    }
}
