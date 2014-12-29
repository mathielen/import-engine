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
        } elseif (is_string($id)) {
            if (!file_exists($id)) {
                throw new \InvalidArgumentException("id is not a valid file path: ".$id);
            }
            $selection = new StorageSelection(new \SplFileInfo($id), $id, $id);

            return $selection;
        } elseif ($id instanceof StorageSelection) {
            return $id;
        }

        throw new \InvalidArgumentException("Id must be a string, an instance of SplFileInfo or a StorageSelection");
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
