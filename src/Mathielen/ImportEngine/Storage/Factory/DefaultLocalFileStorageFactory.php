<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Storage\Type\Discovery\TypeDiscoverStrategyInterface;

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
