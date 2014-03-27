<?php
namespace DataImportEngine\Eventing;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ddeboer\DataImport\Writer\WriterInterface;
use DataImportEngine\Import\Event\ImportEvent;
use Ddeboer\DataImport\Filter\CallbackFilter;
use DataImportEngine\Import\Run\ImportRun;

class ImportEventDispatcher implements WriterInterface
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ImportRun
     */
    private $importRun;

    public function __construct(EventDispatcherInterface $eventDispatcher, ImportRun $importRun)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->importRun = $importRun;
    }

    public function afterReadFilter()
    {
        $eventDispatcher = $this->eventDispatcher;
        $importRun = $this->importRun;

        return new CallbackFilter(function (array $item) use ($eventDispatcher, $importRun) {
            $this->eventDispatcher->dispatch(ImportEvent::AFTER_READ, new ImportEvent($importRun, $item));

            return true;
        });
    }

    public function afterConversionFilter()
    {
        $eventDispatcher = $this->eventDispatcher;
        $importRun = $this->importRun;

        return new CallbackFilter(function (array $item) use ($eventDispatcher, $importRun) {
            $this->eventDispatcher->dispatch(ImportEvent::AFTER_CONVERSION, new ImportEvent($importRun, $item));

            return true;
        });
    }

    /**
     * (non-PHPdoc) @see \Ddeboer\DataImport\Writer\WriterInterface::prepare()
     */
    public function prepare()
    {
        $this->eventDispatcher->dispatch(ImportEvent::START, new ImportEvent($this->importRun));
    }

    /**
     * (non-PHPdoc) @see \Ddeboer\DataImport\Writer\WriterInterface::writeItem()
     */
    public function writeItem(array $item)
    {
        $this->eventDispatcher->dispatch(ImportEvent::AFTER_WRITE, new ImportEvent($this->importRun, $item));
    }

    /**
     * (non-PHPdoc) @see \Ddeboer\DataImport\Writer\WriterInterface::finish()
     */
    public function finish()
    {
        $this->eventDispatcher->dispatch(ImportEvent::FINISH, new ImportEvent($this->importRun));
    }
}
