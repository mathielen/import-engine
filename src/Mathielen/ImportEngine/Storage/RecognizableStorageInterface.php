<?php
namespace Mathielen\ImportEngine\Storage;

interface RecognizableStorageInterface extends StorageInterface
{

    public function getHash();

}
