<?php
namespace Mathielen\ImportEngine\Storage\Factory;

interface StorageFactoryInterface
{

    /**
     * @return StorageInterface
     */
    public function factor($id);

}
