<?php
namespace Mathielen\ImportEngine\ValueObject;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Storage\StorageLocator;
class ImportConfiguration
{

    private $importerId;

    /**
     * @var StorageSelection
     */
    private $sourceStorageSelection;

    /**
     * @var Import
     */
    private $import;

    public function __construct($importerId, StorageSelection $sourceStorageSelection=null)
    {
        $this->setImporterId($importerId);

        if ($sourceStorageSelection) {
            $this->setSourceStorageSelection($sourceStorageSelection);
        }
    }

    public function setSourceStorageSelection(StorageSelection $sourceStorageSelection)
    {
        $this->sourceStorageSelection = $sourceStorageSelection;

        return $this;
    }

    public function setImporterId($importerId)
    {
        $this->importerId = $importerId;

        return $this;
    }

    public function getImporterId()
    {
        return $this->importerId;
    }

    /**
     * @return ImportConfiguration
     */
    public function applyImport(Import $import, StorageLocator $storageLocator)
    {
        $this->import = $import;

        $storage = $storageLocator->getStorage($this->sourceStorageSelection);
        $import->setSourceStorage($storage);

        return $this;
    }

    /**
     * @return Import
     */
    public function getImport()
    {
        return $this->import;
    }

    public function toArray()
    {
        return array(
            'importerid' => $this->importerId,
            'sourcestorageselection' => array(
                'name' => $this->sourceStorageSelection->getName(),
                'id' => $this->sourceStorageSelection->getId()
            )
        );
    }

}
