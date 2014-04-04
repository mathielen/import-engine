<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Storage\Type\Discovery\TypeDiscoverStrategyInterface;
use Mathielen\ImportEngine\Storage\Provider\StorageSelection;

class DefaultLocalFileStorageFactory implements StorageFactoryInterface
{

    /**
     * @var TypeDiscoverStrategyInterface
     */
    private $typeDiscoverStrategyInterface;

    public function __construct(TypeDiscoverStrategyInterface $typeDiscoverStrategyInterface)
    {
        $this->typeDiscoverStrategyInterface = $typeDiscoverStrategyInterface;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface::factor()
     */
    public function factor(StorageSelection $selection)
    {
        $file = $selection->getImpl();

        $type = $this->typeDiscoverStrategyInterface->getType($file->getRealPath());
        if (!$type) {
            throw new \LogicException("Could not discover mimetype!");
        }

        $localFile = new LocalFileStorage($file, $type);

        return $localFile;
    }
}
