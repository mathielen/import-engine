<?php
namespace Mathielen\ImportEngine\ValueObject;

class ImportConfiguration
{

    private $importerId = null;

    /**
     * @var StorageSelection
     */
    private $sourceStorageSelection;

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

    /**
     * @return ImportRun
     */
    public function toRun($createdBy=null)
    {
        return new ImportRun($this, $createdBy);
    }

}
