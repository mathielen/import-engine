<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

class ImporterRepository
{

    /**
     * @var Importer[]
     */
    private $importers = array();

    /**
     * @var ImporterPrecondition[]
     */
    private $preconditions = array();

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function register($id, Importer $importer, ImporterPrecondition $precondition = null)
    {
        $this->importers[$id] = $importer;

        if ($precondition) {
            $this->preconditions[$id] = $precondition;
        }
    }

    /**
     * @return Importer
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->importers)) {
            throw new \InvalidArgumentException("Unknown importer: $id. Register first.");
        }

        return $this->importers[$id];
    }

    /**
     * @return string
     */
    public function find(StorageInterface $storage)
    {
        if ($this->logger) {
            $this->logger->debug("Searching for importer for storage named: '" . $storage->info()['name']."'");
        }

        foreach ($this->preconditions as $importerId => $precondition) {
            if ($this->logger) {
                $this->logger->debug("Checking importer: '" . $importerId."'");
            }

            if ($precondition->isSatisfiedBy($storage, $this->logger)) {
                return $importerId;
            }
        }

        return null;
    }

}
