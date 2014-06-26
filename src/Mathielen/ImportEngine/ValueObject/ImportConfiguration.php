<?php
namespace Mathielen\ImportEngine\ValueObject;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Importer\ImporterRepository;
class ImportConfiguration
{

    private $id;

    public $importerId;
    public $sourceStorageProviderId = 'source';
    public $sourceStorageId;
    public $sourceStorageFormatId;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var Importer
     */
    private $importer;

    public function __construct($importerId)
    {
        $this->id = uniqid();
        $this->importerId = $importerId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getImporter()
    {
        return $this->importer;
    }

    public function getImport()
    {
        return $this->import;
    }

    public function applyImporter(Importer $importer)
    {
        $this->importer = $importer;

        $import = Import::build($importer);
        $this->import = $import;

        $this->applyConfiguration($import);
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
