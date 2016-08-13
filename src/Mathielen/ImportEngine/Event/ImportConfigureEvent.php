<?php

namespace Mathielen\ImportEngine\Event;

use Symfony\Component\EventDispatcher\Event;
use Mathielen\ImportEngine\Import\Import;

class ImportConfigureEvent extends Event
{
    const AFTER_BUILD = 'import-engine.build';

    /**
     * @var Import
     */
    private $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @return Import
     */
    public function getImport()
    {
        return $this->import;
    }
}
