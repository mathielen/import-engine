<?php
namespace Mathielen\DataImport\Event;

use Symfony\Component\EventDispatcher\Event;
use Mathielen\ImportEngine\Import\Import;

class ImportItemEvent extends Event
{

    const AFTER_READ = 'data-import.read';
    const AFTER_FILTER = 'data-import.filter';
    const AFTER_CONVERSION = 'data-import.conversion';
    const AFTER_CONVERSIONFILTER = 'data-import.conversionfilter';
    const AFTER_WRITE = 'data-import.write';
    const AFTER_VALIDATION = 'data-import.validation';

    /**
     * @var array
     */
    private $currentItem;

    public function __construct($item)
    {
        $this->setCurrentResult($item);
    }

    /**
     * @return \Mathielen\DataImport\Event\ImportItemEvent
     */
    public function setCurrentResult($resultOrItem)
    {
        $this->currentItem = $resultOrItem;

        return $this;
    }

    public function getCurrentResult()
    {
        return $this->currentItem;
    }

}
