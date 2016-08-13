<?php

namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\FormatDiscoverLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class FileStorageProvider implements StorageProviderInterface
{
    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    public function __construct(StorageFactoryInterface $storageFactory = null)
    {
        //default
        if (!$storageFactory) {
            $storageFactory = new FormatDiscoverLocalFileStorageFactory(new MimeTypeDiscoverStrategy());
        }

        $this->setStorageFactory($storageFactory);
    }

    public function setStorageFactory(StorageFactoryInterface $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        if ($id instanceof \SplFileInfo) {
            $selection = new StorageSelection($id, $id->getRealPath(), $id->getFilename());

            return $selection;
        } elseif (is_string($id)) {
            if (!file_exists($id)) {
                throw new \InvalidArgumentException('id is not a valid file path: '.$id);
            }
            $selection = new StorageSelection(new \SplFileInfo($id), realpath($id), $id);

            return $selection;
        } elseif ($id instanceof StorageSelection) {
            return $id;
        }

        throw new \InvalidArgumentException('Id must be a string, an instance of SplFileInfo or a StorageSelection. Was: '.print_r($id, true));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        if (!$this->storageFactory) {
            throw new InvalidConfigurationException('Cannot factor storage. StorageFactory is missing. Set factory with setStorageFactory()');
        }

        return $this->storageFactory->factor($selection);
    }
}
