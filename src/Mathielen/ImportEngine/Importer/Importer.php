<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Validation\ValidationInterface;
use Mathielen\ImportEngine\Validation\DummyValidation;
use Mathielen\ImportEngine\Importer\Discovery\DiscoverStrategyInterface;

class Importer
{

    /**
     * @var StorageProviderInterface[]
     */
    private $sourceStorageProviders = array();

    /**
     * @var StorageInterface
     */
    private $sourceStorage;

    /**
     * @var StorageInterface
     */
    private $targetStorage;

    /**
     * @var ValidationInterface
     */
    private $validation;

    /**
     * @var Transformation
     */
    private $transformation;

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public static function build(StorageInterface $targetStorage)
    {
        return new self($targetStorage);
    }

    public function __construct(StorageInterface $targetStorage)
    {
        $this->targetStorage = $targetStorage;

        $this->validation = new DummyValidation();
        $this->transformation = new Transformation();
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function setValidation(ValidationInterface $validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function targetStorage()
    {
        return $this->targetStorage;
    }

    /**
     * @return ValidationInterface
     */
    public function validation()
    {
        return $this->validation;
    }

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function transformation()
    {
        return $this->transformation;
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function addSourceStorageProvider($id, StorageProviderInterface $storageProvider)
    {
        $this->sourceStorageProviders[$id] = $storageProvider;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\StorageProviderInterface[]
     */
    public function getSourceStorageProviders()
    {
        return $this->sourceStorageProviders;
    }

    /**
     * @return StorageProviderInterface
     */
    public function getSourceStorageProvider($id)
    {
        if (!array_key_exists($id, $this->sourceStorageProviders)) {
            throw new \InvalidArgumentException("'$id' not found");
        }

        return $this->sourceStorageProviders[$id];
    }

    /**
     * @return StorageInterface
     */
    public function getSourceStorage()
    {
        return $this->sourceStorage;
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function setSourceStorage(StorageInterface $sourceStorage)
    {
        $this->sourceStorage = $sourceStorage;

        return $this;
    }

}
