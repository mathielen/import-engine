<?php
namespace DataImportEngine\Storage\Provider;

use Symfony\Component\Finder\Finder;
use DataImportEngine\Storage\LocalFileStorage;
use DataImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use DataImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;

class LocalFileStorageProvider extends StorageProvider
{

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
        $this->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy()));
    }

    /**
     * @return Iterator
     */
    public function files()
    {
        return $this->finder->files();
    }

    public function __toString()
    {
        return 'LocalFileStorage';
    }

}
