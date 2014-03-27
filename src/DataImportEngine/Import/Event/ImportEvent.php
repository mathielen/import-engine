<?php
namespace DataImportEngine\Import\Event;

use Symfony\Component\EventDispatcher\Event;
use DataImportEngine\Import\Import;
use DataImportEngine\Import\Run\ImportRun;

class ImportEvent extends Event
{

    const START = 'import-engine.import.start';
    const COMPILE = 'import-engine.import.compile';
    const AFTER_READ = 'import-engine.import.afterread';
    const AFTER_CONVERSION = 'import-engine.import.afterconversion';
    const AFTER_WRITE = 'import-engine.import.afterwrite';
    const FINISH = 'import-engine.import.finish';

    /**
     * @var ImportRun
     */
    private $importRun;

    private $currentRow;

    public function __construct(ImportRun $importRun, array $currentRow = null)
    {
        $this->importRun = $importRun;
        $this->currentRow = $currentRow;
    }

    public function currentRow()
    {
        return $this->currentRow;
    }

}
