<?php
namespace Mathielen\DataImport;

use Ddeboer\DataImport\Workflow as OriginalWorkflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\DataImport\Event\ImportItemEvent;

class EventDispatchableWorkflow extends OriginalWorkflow
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process()
    {
        //if no eventdispatcher has been set, use original functionality
        if (!$this->eventDispatcher) {
            return parent::process();
        }

        $importProcessEvent = new ImportProcessEvent();
        $count = 0;

        //Prepare
        $this->prepare($importProcessEvent);

        // Read all items
        foreach ($this->reader as $item) {
            //only create event once for every item-event for performance reasons
            $event = $importProcessEvent->newItemEvent($item);

            $this->processRead($item, $event);

            // Apply filters before conversion
            if (!$this->processFilter($item, $event)) {
                continue;
            }

            // Convert item
            if (!$convertedItem = $this->processConvert($item, $event)) {
                continue;
            }

            // Apply filters after conversion
            if (!$this->processConvertFilter($convertedItem, $event)) {
                continue;
            }

            $this->processWrite($convertedItem, $item, $event);

            $count++;
        }

        //Finish
        $this->finish($importProcessEvent);

        return $count;
    }

    private function prepare(ImportProcessEvent $importProcessEvent)
    {
        //Prepare writers
        foreach ($this->writers as $writer) {
            $writer->prepare();
        }

        //Send global event
        $this->eventDispatcher->dispatch(ImportProcessEvent::AFTER_PREPARE, $importProcessEvent);
    }

    private function finish(ImportProcessEvent $importProcessEvent)
    {
        // Finish writers
        foreach ($this->writers as $writer) {
            $writer->finish();
        }

        //Send global event
        $this->eventDispatcher->dispatch(ImportProcessEvent::AFTER_FINISH, $importProcessEvent);
    }

    protected function processRead(array $item, ImportItemEvent $event)
    {
        $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_READ, $event);
    }

    protected function processFilter(array $item, ImportItemEvent $event)
    {
        $filterResult = $this->filterItem($item, $this->filters);

        $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_FILTER, $event->setCurrentResult($filterResult));

        return $filterResult;
    }

    protected function processConvert(array $item, ImportItemEvent $event)
    {
        $convertedItem = $this->convertItem($item);

        $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_FILTER, $event->setCurrentResult($convertedItem));

        return $convertedItem;
    }

    protected function processConvertFilter(array $item, ImportItemEvent $event)
    {
        $filterResult = $this->filterItem($item, $this->afterConversionFilters);

        $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_CONVERSIONFILTER, $event->setCurrentResult($filterResult));

        return $filterResult;
    }

    protected function processWrite(array $convertedItem, array $item, ImportItemEvent $event)
    {
        foreach ($this->writers as $writer) {
            $writer->writeItem($convertedItem, $item);
        }

        $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_WRITE, $event);
    }

}
