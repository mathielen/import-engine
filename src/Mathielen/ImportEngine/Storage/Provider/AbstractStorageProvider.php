<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Mathielen\ImportEngine\Storage\StorageInterface;

abstract class AbstractStorageProvider
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

    public function __toString()
    {
        return get_class($this);
    }

}
