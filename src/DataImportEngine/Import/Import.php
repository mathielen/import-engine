<?php
namespace DataImportEngine\Import;

use DataImportEngine\Importer\Importer;
use DataImportEngine\Storage\StorageInterface;
use DataImportEngine\Mapping\Mapping;

class Import
{

    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * @var StorageInterface
     */
    private $targetStorage;

    private $sourceStorageProviderId;
    private $sourceStorageId;
    private $sourceStorageTypeId;
    private $mappingPresetId;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @return \DataImportEngine\Importer\Importer
     */
    public function importer()
    {
        return $this->importer;
    }

    public function availableMappingPresets()
    {
        //TODO
         return array();
    }

    /**
     * @return StorageProviderInterface
     */
    public function getSourceStorageProvider()
    {
        return $this->sourceStorageProviderId?$this->importer->getSourceStorageProvider($this->sourceStorageProviderId):null;
    }

    /**
     * @return StorageInterface
     */
    public function getSourceStorage()
    {
        return $this->sourceStorageId?$this->getSourceStorageProvider()->storage($this->sourceStorageId):null;
    }

    /**
     * @return StorageInterface
     */
    public function getTargetStorage()
    {
        return $this->importer()->getTargetStorage();
    }

    /**
     * @return Mapping
     */
    public function mappings()
    {
        if (!$this->mapping) {
            $this->mapping = $this->importer->buildMapping($this->getSourceStorage()->reader());
        }

        return $this->mapping;
    }

    public function __get($property)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        $mapping = $this->mappings()->getMapping($id);
        if (!$mapping) {
            return null;
        }

        return $mapping[$mappingProperty];
    }

    public function __set($property, $value)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        $this->mappings()->addMapping($id, $value);
    }

    public function getSourceStorageId()
    {
        return $this->sourceStorageId;
    }

    public function setSourceStorageId($sourceStorageId)
    {
        $this->sourceStorageId = $sourceStorageId;

        //initially get auto type
        if ($sourceStorageId) {
           $this->sourceStorageTypeId = $this->getSourceStorage()->getType()->getId();
        } else {
           $this->sourceStorageTypeId = null;
        }

        return $this;
    }

    public function getSourceStorageProviderId()
    {
        return $this->sourceStorageProviderId;
    }

    public function setSourceStorageProviderId($sourceStorageProviderId)
    {
        $this->sourceStorageProviderId = $sourceStorageProviderId;

        $this->setSourceStorageId(null);

        return $this;
    }

    public function getSourceStorageTypeId()
    {
        return $this->sourceStorageTypeId;
    }

    public function setSourceStorageTypeId($sourceStorageTypeId)
    {
        $this->sourceStorageTypeId = $sourceStorageTypeId;

        return $this;
    }

    public function getMappingPresetId()
    {
        return $this->mappingPresetId;
    }

    public function setMappingPresetId($mappingPresetId)
    {
        $this->mappingPresetId = $mappingPresetId;

        return $this;
    }

}
