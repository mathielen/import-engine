<?php
namespace Mathielen\DataImport\Converter;

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
        $this->currentContext = $event->getContext()->getContext();
        if (!is_array($this->currentContext)) {
            throw new \InvalidArgumentException("Context must be an array");
        }
    }

    public function onImportFinish(ImportProcessEvent $event)
    {
        //remove the subscriber when its done
        $event->getDispatcher()->removeSubscriber($this);
    }

    public function convert($input)
    {
        return array_merge($this->currentContext, $input);
    }

} 