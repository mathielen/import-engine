<?php
namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;
use Mathielen\DataImport\Event\ImportProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContextMergeConverter implements ItemConverterInterface, EventSubscriberInterface
{

    private $currentContext;

    public static function getSubscribedEvents()
    {
        return array(
            ImportProcessEvent::AFTER_PREPARE => array('onImportPrepare', 0),
            ImportProcessEvent::AFTER_FINISH => array('onImportFinish', 0),
        );
    }

    public function onImportPrepare(ImportProcessEvent $event)
    {
        //merge converter only works if context is an array
        if (!is_array($event->getContext()->getContext())) {
            return;
        }

        $this->currentContext = $event->getContext()->getContext();
    }

    public function onImportFinish(ImportProcessEvent $event)
    {
        //remove the subscriber when its done
        $event->getDispatcher()->removeSubscriber($this);
    }

    public function convert($input)
    {
        if (!$this->currentContext) {
            return $input;
        }

        return array_merge($this->currentContext, $input);
    }

}
