<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mathielen\ImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;

class UploadFileStorageProvider extends AbstractStorageProvider
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

}
