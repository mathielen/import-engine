<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
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
     * @return id
     */
    public function find(StorageInterface $storage)
    {
        foreach ($this->preconditions as $importerId => $precondition) {
            if ($precondition->isSatisfiedBy($storage)) {
                return $importerId;
            }
        }

        return null;
    }

}
