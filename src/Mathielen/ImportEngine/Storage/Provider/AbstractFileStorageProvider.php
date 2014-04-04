<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;

abstract class AbstractFileStorageProvider implements StorageProviderInterface
{

    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    public function setStorageFactory(StorageFactoryInterface $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        if (!$this->storageFactory) {
            throw new \LogicException('StorageFactory is missing.');
        }

        return $this->storageFactory->factor($selection);
    }

}
