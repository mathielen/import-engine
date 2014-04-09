<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;
interface StorageFactoryInterface
{

    /**
     * @return StorageInterface
     */
    public function factor(StorageSelection $selection);

}
