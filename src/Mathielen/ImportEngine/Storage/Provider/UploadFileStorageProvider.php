<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

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
            $newFile = $id->move($this->targetDirectory, $this->generateTargetFilename($id));

            $selection = new StorageSelection(
                new \SplFileInfo($newFile),
                $this->targetDirectory.  '/' . $newFile->getFilename(),
                $id->getClientOriginalName());

            return $selection;
        }

        return parent::select($id);
    }

    private function generateTargetFilename(UploadedFile $file)
    {
        return uniqid() . '_' . $file->getClientOriginalName();
    }

}
