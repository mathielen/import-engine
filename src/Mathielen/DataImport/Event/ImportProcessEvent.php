<?php
namespace Mathielen\DataImport\Event;

use Symfony\Component\EventDispatcher\Event;
use Mathielen\ImportEngine\Import\Import;

class ImportProcessEvent extends Event
{

    const AFTER_PREPARE = 'data-import.prepare';
    const AFTER_FINISH = 'data-import.finish';

}
