<?php
namespace DataImportEngine\Storage\Provider;

use DataImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DataImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;

class UploadFileStorageProvider extends StorageProvider
{

    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        if (!is_dir($targetDirectory) || !is_writeable($targetDirectory)) {
            throw new \InvalidArgumentException("Targetdirectory $targetDirectory is not writable!");
        }

        $this->targetDirectory = realpath($targetDirectory);
        $this->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy()));
    }

    public function storage($id)
    {
        if ($id instanceof UploadedFile && $id->isValid()) {
            $id->move($this->targetDirectory, $id->getClientOriginalName());
            $id = $id->getClientOriginalName();
        }

        return parent::storage($this->targetDirectory . '/' . $id);
    }

    public function __toString()
    {
        return 'UploadFileStorageProvider';
    }

}
