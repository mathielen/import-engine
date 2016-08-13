<?php

namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Importer\ImporterInterface;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Mapping\Mappings;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class Import
{
    /**
     * @var ImporterInterface
     */
    private $importer;

    /**
     * @var Transformation
     */
    private $transformation;

    /**
     * @var StorageInterface
     */
    private $sourceStorage;

    /**
     * @var Mappings
     */
    private $mappings;

    /**
     * @var ImportRun
     */
    private $importRun;

    /**
     * @return Import
     */
    public static function build(ImporterInterface $importer, StorageInterface $sourceStorage, ImportRun $importRun = null)
    {
        return new self($importer, $sourceStorage, $importRun);
    }

    public function __construct(ImporterInterface $importer, StorageInterface $sourceStorage, ImportRun $importRun = null)
    {
        if (!$importRun) {
            $importRun = new ImportRun();
        }

        $this->importer = $importer;
        $this->sourceStorage = $sourceStorage;
        $this->importRun = $importRun;
        $this->transformation = $importer->transformation();
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\ImporterInterface
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
    public function getTargetStorage()
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
     * @return ImportRun
     */
    public function getRun()
    {
        return $this->importRun;
    }
}
