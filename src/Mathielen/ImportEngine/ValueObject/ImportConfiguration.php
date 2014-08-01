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

    public function __construct(StorageSelection $sourceStorageSelection=null, $importerId=null)
    {
        if ($importerId) {
            $this->setImporterId($importerId);
        }
        if ($sourceStorageSelection) {
            $this->setSourceStorageSelection($sourceStorageSelection);
        }
    }

    /**
     * @return ImportConfiguration
     */
    public function setSourceStorageSelection(StorageSelection $sourceStorageSelection)
    {
        $this->sourceStorageSelection = $sourceStorageSelection;

        return $this;
    }

    /**
     * @return StorageSelection
     */
    public function getSourceStorageSelection()
    {
        return $this->sourceStorageSelection;
    }

    /**
     * @return ImportConfiguration
     */
    public function setImporterId($importerId)
    {
        if (empty($importerId)) {
            throw new \InvalidArgumentException("importerId must be given");
        }

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
            'sourcestorageselection' => $this->sourceStorageSelection?array(
                'name' => $this->sourceStorageSelection->getName(),
                'id' => $this->sourceStorageSelection->getId()
            ):null
        );
    }

}
