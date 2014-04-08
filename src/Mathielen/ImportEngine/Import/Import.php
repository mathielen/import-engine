<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Mapping\Mapping;
use Mathielen\ImportEngine\Validation\Validation;
use Mathielen\ImportEngine\Mapping\Mappings;
use Mathielen\DataImport\Workflow;
use Mathielen\ImportEngine\Storage\StorageSubtypeInterface;
use Mathielen\ImportEngine\Storage\Provider\StorageSelection;

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

    /**
     * @var StorageInterface
     */
    private $sourceStorage;

    private $sourceStorageProviderId;
    private $sourceStorageId;
    private $sourceStorageSubtypeId;
    private $dryrun;

    /**
     * @return \Mathielen\ImportEngine\Importer\Import
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
     * @return \Mathielen\ImportEngine\Importer\Importer
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
        if (empty($this->sourceStorageProviderId)) {
            throw new \LogicException("sourceStorageProviderId must be set first.");
        }

        return $this->importer->getSourceStorageProvider($this->sourceStorageProviderId);
    }

    /**
     * @return StorageInterface
     */
    public function getSourceStorage()
    {
        if (!$this->sourceStorage && $this->sourceStorageId) {
            return $this->getSourceStorageProvider()->storage($this->sourceStorageId);
        }

        return $this->sourceStorage;
    }

    public function setSourceStorage(StorageInterface $sourceStorage)
    {
        $this->sourceStorage = $sourceStorage;

        return $this;
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

    /**
     * @return \Mathielen\ImportEngine\Validation\Validation
     */
    public function validation()
    {
        return $this->importer()->validation();
    }

    //set and get values to mapping (needs to be magic, as the import is the VO in forms)
    public function __get($property)
    {
        @list ($mappingProperty, $id) = explode('_', $property);
        if (!$id) {
            return;
        }

        $fields = $this->getSourceStorage()->getFields();
        $fieldName = $fields[$id];

        $mapping = $this->mappings()->get($fieldName);
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

        $fields = $this->getSourceStorage()->getFields();
        $fieldName = $fields[$id];

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

        if (!empty($sourceStorageId) && !($sourceStorageId instanceof StorageSelection)) {
            $sourceStorageId = $this->getSourceStorageProvider()->select($sourceStorageId);
        }

        $this->sourceStorageId = $sourceStorageId;

        //initially get auto type
        if ($sourceStorageId && $this->getSourceStorage() instanceof StorageSubtypeInterface) {
            $this->sourceStorageSubtypeId = $this->getSourceStorage()->getType()->getId();
        } else {
            $this->sourceStorageSubtypeId = null;
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
        $this->sourceStorage = null;

        return $this;
    }

    public function getSourceStorageSubtypeId()
    {
        return $this->sourceStorageSubtypeId;
    }

    public function setSourceStorageSubtypeId($sourceStorageSubtypeId)
    {
        if (!($this->getSourceStorage() instanceof StorageSubtypeInterface)) {
            throw new \LogicException("Storage offers no subtype support");
        }
        if (!in_array($sourceStorageSubtypeId, $this->getSourceStorage()->getAvailableTypes())) {
            throw new \InvalidArgumentException("Invalid subtype given: ".$sourceStorageSubtypeId);
        }

        $this->sourceStorageSubtypeId = $sourceStorageSubtypeId;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Import\Import
     */
    public function applyValidation(Workflow $workflow)
    {
        $this->validation() ? $this->validation()->apply($workflow) : null;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Import\Import
     */
    public function applyMapping(Workflow $workflow, array $converters)
    {
        $this->mappings()->apply($workflow, $converters);

        return $this;
    }

    public function setDryrun($dryrun)
    {
        $this->dryrun = $dryrun;
    }

    public function isDryrun()
    {
        return $this->dryrun;
    }

}
