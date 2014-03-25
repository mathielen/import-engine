<?php
namespace DataImportEngine\Storage\Factory;

interface StorageFactoryInterface
{

    /**
     * @return StorageInterface
     */
    public function factor($id);

}
