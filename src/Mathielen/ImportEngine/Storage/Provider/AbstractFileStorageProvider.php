<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Mathielen\ImportEngine\Storage\StorageInterface;

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
     * @return StorageInterface
     */
    public function storage($id)
    {
        if (!$this->storageFactory) {
            throw new \LogicException('StorageFactory is missing.');
        }

        return $this->storageFactory->factor($id);
    }
}
