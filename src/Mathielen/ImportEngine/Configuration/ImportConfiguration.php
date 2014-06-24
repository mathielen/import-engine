<?php
namespace Mathielen\ImportEngine\Configuration;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;
class ImportConfiguration
{

    private $id;

    public $sourceStorageProviderId = 'source';
    public $sourceStorageId;
    public $sourceStorageFormatId;

    /**
     * @var Importer
     */
    private $importer;

    public function __construct(Importer $importer)
    {
        $this->id = uniqid();
        $this->importer = $importer;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Importer
     */
    public function importer()
    {
        return $this->importer;
    }

    /**
     * @return \Mathielen\ImportEngine\Import\Import
     */
    public function buildImport()
    {
        $import = Import::build($this->importer);
        $this->applyConfiguration($import);

        return $import;
    }

    private function applyConfiguration(Import $import)
    {
        if ($this->sourceStorageId instanceof StorageInterface) {
            $import->setSourceStorage($this->sourceStorageId);

        } elseif ($this->sourceStorageProviderId) {
            $sourceStorageProvider = $import->importer()->getSourceStorageProvider($this->sourceStorageProviderId);
            if ($this->sourceStorageId) {
                //wrap to StorageSelection, if plain data & possible
                if (!$this->sourceStorageId instanceof StorageSelection) {
                    $this->sourceStorageId = $sourceStorageProvider->select($this->sourceStorageId);
                }

                $sourceStorage = $sourceStorageProvider->storage($this->sourceStorageId);
                $import->setSourceStorage($sourceStorage);
            }

        } else {
            throw new \InvalidArgumentException("SourceStorageId is missing or does not implement StorageInterface");
        }
    }

    //set and get values to mapping (needs to be magic, as the import is the VO in forms)
    public function __get($property)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        if (!$id) {
            return;
        }

        $fields = $this->import()->getSourceStorage()->getFields();
        $fieldName = $fields[$id];

        $mapping = $this->import()->mappings()->get($fieldName);
        if (!$mapping) {
            return null;
        }

        return $mapping->$mappingProperty;
    }

    public function __set($property, $value)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        if (!$id) {
            return;
        }

        $fields = $this->import()->getSourceStorage()->getFields();
        $fieldName = $fields[$id];

        if ($mappingProperty == 'to') {
            $this->import()->mappings()->add($fieldName, $value);
        } elseif ($mappingProperty == 'converter') {
            $this->import()->mappings()->setConverter($fieldName, $value);
        }
    }

}
