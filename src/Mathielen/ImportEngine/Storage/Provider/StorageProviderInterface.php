<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\StorageInterface;

interface StorageProviderInterface
{

    /**
     * @return StorageInterface
     */
    public function storage($id);

}
