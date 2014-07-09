<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

abstract class AbstractFileStorageProvider implements StorageProviderInterface
{

    /**
     * @var StorageFactoryInterface
     */
    private $storageFactory;

    public function setStorageFactory(StorageFactoryInterface $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        if ($id instanceof \SplFileInfo) {
            $selection = new StorageSelection($id, $id->getFilename(), $id->getFilename());
            return $selection;
        } elseif (!($id instanceof StorageSelection)) {
            throw new \InvalidArgumentException("Id must be an Instance of SplFileInfo or StorageSelection");
        }

        return $id;
    }



    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        if (!$this->storageFactory) {
            throw new InvalidConfigurationException('StorageFactory is missing.');
        }

        return $this->storageFactory->factor($selection);
    }

}
