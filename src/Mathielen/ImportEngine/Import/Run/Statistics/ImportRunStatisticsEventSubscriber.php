<?php
namespace Mathielen\ImportEngine\Import\Run\Statistics;

use Mathielen\DataImport\Event\ImportProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mathielen\DataImport\Event\ImportItemEvent;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class ImportRunStatisticsEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var ImportRun
     */
    private $importrun;

    private $statistics;

    public function __construct(ImportRun $importrun)
    {
        $this->importrun = $importrun;
        $this->statistics = array(
            'processed' => 0,
            'written' => 0,
            'skipped' => 0,
            'invalid' => 0
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
        $event->setContext($this->importrun);
    }

    public function onImportFinish(ImportProcessEvent $event)
    {
        //remove the subscriber when its done
        $event->getDispatcher()->removeSubscriber($this);
    }

    public function onAfterRead(ImportItemEvent $event)
    {
        ++$this->statistics['processed'];

        $this->importrun->setStatistics($this->statistics);
        $this->importrun->finish();
    }

    public function onAfterFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->importrun->setStatistics($this->statistics);
        }
    }

    public function onAfterConversion(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->importrun->setStatistics($this->statistics);
        }
    }

    public function onAfterConversionFilter(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['skipped'];

            $this->importrun->setStatistics($this->statistics);
        }
    }

    public function onAfterValidate(ImportItemEvent $event)
    {
        if (!$event->getCurrentResult()) {
            ++$this->statistics['invalid'];

            $this->importrun->setStatistics($this->statistics);
        }
    }

    public function onAfterWrite(ImportItemEvent $event)
    {
        ++$this->statistics['written'];

        $this->importrun->setStatistics($this->statistics);
    }
}
