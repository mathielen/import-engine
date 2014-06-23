<?php
namespace Mathielen\ImportEngine\Configuration;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Importer\Importer;
class ImportConfiguration
{

    public $sourceStorageProviderId = 'source';
    public $sourceStorageId;
    public $sourceStorageFormatId;
    public $dryrun = false;

    private $import;
    private $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @return Import
     */
    public function import()
    {
        return $this->import;
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
        $import = new Import($this->importer);

        if ($this->sourceStorageProviderId) {
            $sourceStorageProvider = $import->importer()->getSourceStorageProvider($this->sourceStorageProviderId);
            if ($this->sourceStorageId) {
                $storage = $sourceStorageProvider->storage($this->sourceStorageId);
                $import->setSourceStorage($storage);
            }
        }

        $this->import = $import;
        return $import;
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
