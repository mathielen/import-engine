<?php

namespace Mathielen\ImportEngine\Exception;

use Mathielen\ImportEngine\ValueObject\ImportRun;

class ImportRunException extends ImportException
{
    /**
     * @var ImportRun
     */
    private $importRun;

    public function __construct($message, ImportRun $importRun, \Exception $previous = null)
    {
        parent::__construct($message."\n".print_r($importRun, true), 0, $previous);
        $this->importRun = $importRun;
    }

    /**
     * @return ImportRun
     */
    public function getImportRun()
    {
        return $this->importRun;
    }
}
