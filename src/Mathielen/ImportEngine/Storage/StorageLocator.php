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

        return $provider->select($id);
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(StorageSelection $selection)
    {
        $provider = $this->get($selection->getProviderId());

        return $provider->storage($selection);
    }

}
