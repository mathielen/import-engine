<?php
namespace Mathielen\ImportEngine\Storage;

interface RecognizableStorageInterface extends StorageInterface
{

    /**
     * @return string
     */
    public function getHash();

}
