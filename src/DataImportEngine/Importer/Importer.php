<?php
namespace DataImportEngine\Importer;

use DataImportEngine\Storage\Provider\StorageProvider;
use DataImportEngine\Mapping\DefaultMappingFactory;
use DataImportEngine\Mapping\MappingFactoryInterface;
use DataImportEngine\Storage\StorageInterface;
use Ddeboer\DataImport\Reader\ReaderInterface;
use DataImportEngine\Mapping\Converter\Provider\ConverterProviderInterface;
use DataImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use DataImportEngine\Validation\Validation;

class Importer
{

    private $sourceStorageProviders = array();
    private $targetStorage;

    /**
     * @var MappingFactoryInterface
     */
    private $mappingFactory;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var ConverterProviderInterface
     */
    private $mappingConverterProvider;

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public static function build(StorageInterface $targetStorage)
    {
        return new self($targetStorage);
    }

    public function __construct(StorageInterface $targetStorage)
    {
        $this->targetStorage = $targetStorage;

        $this->setMappingFactory(new DefaultMappingFactory());
        $this->setMappingConverterProvider(new DefaultConverterProvider());
    }

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public function setMappingConverterProvider(ConverterProviderInterface $mappingConverterProvider)
    {
        $this->mappingConverterProvider = $mappingConverterProvider;

        return $this;
    }

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public function setMappingFactory(MappingFactoryInterface $mappingFactory)
    {
        $this->mappingFactory = $mappingFactory;

        return $this;
    }

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public function setValidation(Validation $validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public function addSourceStorageProvider($id, StorageProvider $storageProvider)
    {
        $this->sourceStorageProviders[$id] = $storageProvider;

        return $this;
    }

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
    public function getTargetStorage()
    {
        return $this->targetStorage;
    }

    /**
     * @return Mappings
     */
    public function buildMappings(ReaderInterface $reader)
    {
        return $this->mappingFactory->factor($reader);
    }

    /**
     * @return Validation
     */
    public function validation()
    {
        return $this->validation;
    }

    public function converters()
    {
        return $this->mappingConverterProvider->converters();
    }

    public function converter($id)
    {
        return $this->mappingConverterProvider->converter($id);
    }

}
