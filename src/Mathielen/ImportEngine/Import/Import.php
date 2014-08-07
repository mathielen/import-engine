<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Mapping\Mappings;

class Import
{

    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var Transformation
     */
    private $transformation;

    /**
     * @var StorageInterface
     */
    private $targetStorage;

    /**
     * @var StorageInterface
     */
    private $sourceStorage;

    /**
     * @var Mappings
     */
    private $mappings;

    /**
     * @return Import
     */
    public static function build(Importer $importer)
    {
        return new self($importer);
    }

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
        $this->transformation = new Transformation();

        if ($importer->getSourceStorage()) {
            $this->setSourceStorage($importer->getSourceStorage());
        }
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function importer()
    {
        return $this->importer;
    }

    /**
     * @return Mappings
     */
    public function mappings()
    {
        if (!$this->mappings && $this->getSourceStorage()) {
            $this->mappings = $this->transformation->buildMapping($this->getSourceStorage()->reader());
        }

        return $this->mappings;
    }

    /**
     * @return \Mathielen\ImportEngine\Storage\StorageInterface
     */
    public function targetStorage()
    {
        return $this->importer->targetStorage();
    }

    /**
     * @return \Mathielen\ImportEngine\Storage\StorageInterface
     */
    public function getSourceStorage()
    {
        return $this->sourceStorage;
    }

    /**
     * @return \Mathielen\ImportEngine\Import\Import
     */
    public function setSourceStorage(StorageInterface $storage)
    {
        $this->sourceStorage = $storage;

        return $this;
    }

}
