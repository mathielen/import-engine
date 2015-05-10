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
        if (!isset($this->importers[$id])) {
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
            $this->logger->debug("Searching for importer for storage");
        }

        foreach ($this->preconditions as $importerId => $precondition) {
            if ($this->logger) {
                $this->logger->debug("Checking preconditions for importer: '" . $importerId . "'");
            }

            if ($precondition->isSatisfiedBy($storage, $this->logger)) {
                if ($this->logger) {
                    $this->logger->debug("Preconditions matched for importer: '" . $importerId . "'. Using this import.");
                }

                return $importerId;
            }
        }

        return null;
    }

}
