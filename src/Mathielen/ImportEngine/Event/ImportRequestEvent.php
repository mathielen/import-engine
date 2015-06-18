<?php
namespace Mathielen\ImportEngine\Event;

use Mathielen\ImportEngine\ValueObject\ImportRequest;
use Symfony\Component\EventDispatcher\Event;

class ImportRequestEvent extends Event
{

    const DISCOVERED = 'import-engine.importer.discovered';

    /**
     * @var ImportRequest
     */
    private $importRequest;

    public function __construct(ImportRequest $importRequest)
    {
        $this->importRequest = $importRequest;
    }

    /**
     * @return ImportRequest
     */
    public function getImportRequest()
    {
        return $this->importRequest;
    }

}
