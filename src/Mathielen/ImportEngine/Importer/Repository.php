<?php
namespace Mathielen\ImportEngine\Importer;

class Repository
{

    /**
     * @var Importer[]
     */
    private $importers = array();

    public function register($id, Importer $importer)
    {
        $this->importers[$id] = $importer;
    }

    public function get($id)
    {
        if (!array_key_exists($id, $this->importers)) {
            throw new \InvalidArgumentException("Unknown importer: $id. Register first.");
        }

        return $this->importers[$id];
    }

}
