<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mathielen\ImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;

class UploadFileStorageProvider extends AbstractFileStorageProvider
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

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id)
    {
        if ($id instanceof UploadedFile && $id->isValid()) {
            $newFile = $id->move($this->targetDirectory, uniqid() . $id->getClientOriginalName());

            $selection = new StorageSelection(
                $this->targetDirectory.  '/' . $newFile->getFilename(),
                $id->getClientOriginalName(),
                new \SplFileObject($newFile));
        } else {
            throw new \InvalidArgumentException("Not an uploadedfile: ".print_r($id, true));
        }

        return $selection;
    }

}
