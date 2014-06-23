<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\Import;

class ImportRun
{

    protected $id;
    protected $statistics;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setStatistics(array $statistics)
    {
        return $this->statistics = $statistics;
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

}
