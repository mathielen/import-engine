<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;

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
    public function select($id = null)
    {
        if ($id instanceof UploadedFile && $id->isValid()) {
            $newFile = $id->move($this->targetDirectory, uniqid() . $id->getClientOriginalName());

            $selection = new StorageSelection(
                new \SplFileObject($newFile),
                $this->targetDirectory.  '/' . $newFile->getFilename(),
                $id->getClientOriginalName());
        } else {
            throw new \InvalidArgumentException("Not an uploadedfile: ".print_r($id, true));
        }

        return $selection;
    }

}
