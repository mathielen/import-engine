<?php
namespace DataImportEngine\Storage\Provider;

use DataImportEngine\Storage\Factory\StorageFactoryInterface;

class StorageProvider
{

    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    public function setStorageFactory(StorageFactoryInterface $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    public function storage($id)
    {
        return $this->storageFactory->factor($id);
    }

}
