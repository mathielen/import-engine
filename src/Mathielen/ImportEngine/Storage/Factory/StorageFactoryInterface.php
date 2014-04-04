<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\Provider\StorageSelection;
interface StorageFactoryInterface
{

    /**
     * @return StorageInterface
     */
    public function factor(StorageSelection $selection);

}
