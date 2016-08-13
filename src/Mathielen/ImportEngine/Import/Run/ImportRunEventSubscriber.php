<?php

namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\ImportEngine\Import\Import;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mathielen\DataImport\Event\ImportItemEvent;

class ImportRunEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Import
     */
    private $import;

    private $statistics;

    private $isDryRun;

    public function __construct(Import $import, $isDryRun = false)
    {
        $this->isDryRun = $isDryRun;
        $this->import = $import;
        $this->statistics = array(
            'processed' => 0,
            'written' => 0,
            'skipped' => 0,
            'invalid' => 0,
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            ImportProcessEvent::AFTER_PREPARE => array('onImportPrepare', 999999),
            ImportItemEvent::AFTER_READ => array('onAfterRead', 0),
            ImportItemEvent::AFTER_FILTER => array('onAfterFilter', 0),
            ImportItemEvent::AFTER_CONVERSION => array('onAfterConversion', 0),
            ImportItemEvent::AFTER_CONVERSIONFILTER => array('onAfterConversionFilter', 0),
            ImportItemEvent::AFTER_VALIDATION => array('onAfterValidate', 0),
            ImportItemEvent::AFTER_WRITE => array('onAfterWrite', 0),
            ImportProcessEvent::AFTER_FINISH => array('onImportFinish', 999999),
        );
    }

    public function onImportPrepare(ImportProcessEvent $event)
    {
        $event->setContext($this->import);
    }

    public function onImportFinish(ImportProcessEvent $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        if (!$this->isDryRun) {
            $this->import->getRun()->finish();
        }

        //remove the subscriber when its done
        $eventDispatcher->removeSubscriber($this);
    }

    public function onAfterRead(ImportItemEvent $event)
    {
        ++$this->statistics['processed'];

        $this->import->getRun()->setStatistics($this->statistics);
    }

    public function onAfterFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->import->getRun()->setStatistics($this->statistics);
        }
    }

    public function onAfterConversion(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->import->getRun()->setStatistics($this->statistics);
        }
    }

    public function onAfterConversionFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->import->getRun()->setStatistics($this->statistics);
        }
    }

    public function onAfterValidate(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['invalid'];

            $this->import->getRun()->setStatistics($this->statistics);
        }
    }

    public function onAfterWrite(ImportItemEvent $event)
    {
        ++$this->statistics['written'];

        $this->import->getRun()->setStatistics($this->statistics);
    }
}
