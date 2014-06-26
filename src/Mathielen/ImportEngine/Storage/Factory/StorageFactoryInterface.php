<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\ValueObject\StorageSelection;
interface StorageFactoryInterface
{

    /**
     * @return StorageInterface
     */
    public function factor(StorageSelection $selection);

}
