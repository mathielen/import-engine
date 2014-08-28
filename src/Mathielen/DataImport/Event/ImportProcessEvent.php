<?php
namespace Mathielen\DataImport\Event;

use Symfony\Component\EventDispatcher\Event;

class ImportProcessEvent extends Event
{

    const AFTER_PREPARE = 'data-import.prepare';
    const AFTER_FINISH = 'data-import.finish';

    private $context;

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return ImportItemEvent
     */
    public function newItemEvent($item)
    {
        $event = new ImportItemEvent($item);
        $event->setContext($this->context);

        return $event;
    }

}
