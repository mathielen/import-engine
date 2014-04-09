<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;

interface StorageProviderInterface
{

    /**
     * Return a StorageHandler for a specific selection
     *
     * @return StorageInterface
     */
    public function storage(StorageSelection $selection);

    /**
     * Convert/cast an arbitrary selection (string, splfileobject, etc) to a known
     * one. The StorageProvider must know how to convert it
     *
     * @return StorageSelection $selection
     */
    public function select($id = null);

}
