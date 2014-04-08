<?php
namespace Mathielen\ImportEngine\Import\Run\Statistics;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mathielen\DataImport\Event\ImportItemEvent;

class ImportRunStatisticsEventSubscriber implements EventSubscriberInterface
{

    private $statistics;

    public function __construct()
    {
        $this->statistics = array(
            'processed' => 0,
            'written' => 0,
            'skipped' => 0,
            'invalid' => 0
        );
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ImportItemEvent::AFTER_READ => array('onAfterRead', 0),
            ImportItemEvent::AFTER_FILTER => array('onAfterFilter', 0),
            ImportItemEvent::AFTER_CONVERSION => array('onAfterConversion', 0),
            ImportItemEvent::AFTER_CONVERSIONFILTER => array('onAfterConversionFilter', 0),
            ImportItemEvent::AFTER_VALIDATION => array('onAfterValidate', 0),
            ImportItemEvent::AFTER_WRITE => array('onAfterWrite', 0)
        );
    }

    public function onAfterRead(ImportItemEvent $event)
    {
        ++$this->statistics['processed'];
    }

    public function onAfterFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];
        }
    }

    public function onAfterConversion(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];
        }
    }

    public function onAfterConversionFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];
        }
    }

    public function onAfterValidate(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['invalid'];
        }
    }

    public function onAfterWrite(ImportItemEvent $event)
    {
        ++$this->statistics['written'];
    }
}
