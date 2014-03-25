<?php
namespace DataImportEngine\Storage\Factory;

use DataImportEngine\Storage\LocalFileStorage;
use DataImportEngine\Storage\Type\Discovery\TypeDiscoverStrategyInterface;

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
     * @see \DataImportEngine\Storage\Factory\StorageFactoryInterface::factor()
     */
    public function factor($id)
    {
        $file = new \SplFileObject($id);

        $type = $this->typeDiscoverStrategyInterface->getType($file->getRealPath());
        if (!$type) {
            throw new \LogicException("Could not discover mimetype!");
        }

        $localFile = new LocalFileStorage($file, $type);

        return $localFile;
    }
}
