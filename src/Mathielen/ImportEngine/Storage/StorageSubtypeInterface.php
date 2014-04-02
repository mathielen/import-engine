<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\ImportEngine\Storage\Type\Type;

interface StorageSubtypeInterface extends StorageInterface
{

    /**
     * @return Type
     */
    public function getType();

    /**
     * @return array
     */
    public function getAvailableTypes();

}
