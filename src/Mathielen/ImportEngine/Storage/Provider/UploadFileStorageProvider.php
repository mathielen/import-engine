<?php

namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class UploadFileStorageProvider extends FileStorageProvider
{
    private $targetDirectory;

    public function __construct($targetDirectory, StorageFactoryInterface $storageFactory = null)
    {
        parent::__construct($storageFactory);

        if (!is_dir($targetDirectory) || !is_writeable($targetDirectory)) {
            throw new \InvalidArgumentException("Targetdirectory $targetDirectory is not writable!");
        }

        $this->targetDirectory = realpath($targetDirectory);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        if ($id instanceof UploadedFile) {
            if (!$id->isValid()) {
                throw new \InvalidArgumentException('Upload was not successful');
            }

            $newFile = $id->move($this->targetDirectory, $this->generateTargetFilename($id));

            $selection = new StorageSelection(
                new \SplFileInfo($newFile),
                $this->targetDirectory.'/'.$newFile->getFilename(),
                $id->getClientOriginalName());

            return $selection;
        }

        return parent::select($id);
    }

    private function generateTargetFilename(UploadedFile $file)
    {
        return uniqid().'_'.$file->getClientOriginalName();
    }
}
