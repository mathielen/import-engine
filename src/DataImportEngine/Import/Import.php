<?php
namespace DataImportEngine\Import;

use DataImportEngine\Importer\Importer;
use DataImportEngine\Storage\StorageInterface;
use DataImportEngine\Mapping\Mapping;
use DataImportEngine\Validation\Validation;
use DataImportEngine\Mapping\Mappings;

class Import
{

    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var Mappings
     */
    private $mappings;

    /**
     * @var StorageInterface
     */
    private $targetStorage;

    private $sourceStorageProviderId;
    private $sourceStorageId;
    private $sourceStorageTypeId;

    /**
     * @return \DataImportEngine\Importer\Import
     */
    public static function build(Importer $importer)
    {
        return new self($importer);
    }

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
        return ($this->sourceStorageId && $this->getSourceStorageProvider())?$this->getSourceStorageProvider()->storage($this->sourceStorageId):null;
    }

    /**
     * @return StorageInterface
     */
    public function getTargetStorage()
    {
        return $this->importer()->getTargetStorage();
    }

    /**
     * @return Mappings
     */
    public function mappings()
    {
        if (!$this->mappings) {
            $this->mappings = $this->importer->buildMappings($this->getSourceStorage()->reader());
        }

        return $this->mappings;
    }

    public function validation()
    {
        return new Validation();
    }

    //set and get values to mapping (needs to be magic, as the import is the VO in forms)
    public function __get($property)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        $fieldName = $this->getSourceStorage()->getFields()[$id];

        $mapping = $this->mappings()->get($fieldName);
        if (!$mapping) {
            return null;
        }

        return $mapping->$mappingProperty;
    }

    public function __set($property, $value)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        $fieldName = $this->getSourceStorage()->getFields()[$id];

        if ($mappingProperty == 'to') {
            $this->mappings()->add($fieldName, $value);
        } elseif ($mappingProperty == 'converter') {
            $this->mappings()->setConverter($fieldName, $value);
        }
    }

    public function getSourceStorageId()
    {
        return $this->sourceStorageId;
    }

    public function setSourceStorageId($sourceStorageId)
    {
        if (!$this->getSourceStorageProvider()) {
            throw new \LogicException("Cannot set sourceStorage without setting sourceStorageProvider first");
        }

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

}
