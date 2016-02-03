<?php
namespace Mathielen\ImportEngine\Storage\Provider\Connection;

use Mathielen\ImportEngine\ValueObject\StorageSelection;

interface ConnectionFactoryInterface
{

    public function getConnection(StorageSelection $selection = null);

}
