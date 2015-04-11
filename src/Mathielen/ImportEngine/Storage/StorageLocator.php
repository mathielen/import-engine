<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class StorageLocator
{

    /**
     * @var StorageProviderInterface[]
     */
    private $providers = array();

    /**
     * @var \SplObjectStorage
     */
    private $storageCache;

    public function __construct()
    {
        $this->storageCache = new \SplObjectStorage();
    }

    public function register($idProvider, StorageProviderInterface $provider)
    {
        $this->providers[$idProvider] = $provider;
    }

    /**
     * @return StorageProviderInterface
     */
    public function get($idProvider)
    {
        if (!array_key_exists($idProvider, $this->providers)) {
            throw new \InvalidArgumentException("Provider with id '$idProvider' does not exist!");
        }

        return $this->providers[$idProvider];
    }

    /**
     * @return StorageSelection
     */
    public function selectStorage($idProvider, $id)
    {
        $provider = $this->get($idProvider);

        return $provider
            ->select($id)
            ->setProviderId($idProvider);
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(StorageSelection $selection)
    {
        if (!isset($this->storageCache[$selection])) {
            $provider = $this->get($selection->getProviderId());
            $storage = $provider->storage($selection);

            $this->storageCache[$selection] = $storage;
        }

        return $this->storageCache[$selection];
    }

}
