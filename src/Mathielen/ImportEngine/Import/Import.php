<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Mapping\Mappings;
use Mathielen\ImportEngine\ValueObject\ImportRun;

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
    public static function build(Importer $importer, StorageInterface $sourceStorage, ImportRun $importRun=null)
    {
        return new self($importer, $sourceStorage, $importRun);
    }

    public function __construct(Importer $importer, StorageInterface $sourceStorage, ImportRun $importRun=null)
    {
        $this->importer = $importer;
        $this->sourceStorage = $sourceStorage;
        $this->importRun = $importRun;
        $this->transformation = new Transformation();
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
