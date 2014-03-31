<?php
namespace DataImportEngine\Import\Run;

use DataImportEngine\Import\Import;

class ImportRun
{

    private $id;
    private $rowsProcessed = 0;
    private $rowsWritten = 0;
    private $rowsSkipped = 0;
    private $rowsInvalid = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

}
