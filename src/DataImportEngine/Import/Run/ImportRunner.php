<?php
namespace DataImportEngine\Import\Run;

use DataImportEngine\Import\Import;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DataImportEngine\Import\Event\ImportEvent;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Ddeboer\DataImport\Writer\ArrayWriter;
use DataImportEngine\Import\Event\ImportEventDispatcher;
use DataImportEngine\Import\Filter\PriorityCallbackFilter;

class ImportRunner
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function preview(Import $import, $offset = 0)
    {
        $previewResult = array('from'=>array(), 'to'=>array());

        $workflow = $this->buildPreviewWorkflow($import, $previewResult, $offset);
        $workflow->process();

        //cleanup from writer
        if (count($previewResult['to']) > 0) {
            $previewResult['to'] = $previewResult['to'][0];
        } else {
            $previewResult['to'] = array_fill_keys($import->mappings()->getTargetFields(), null);
        }

        return $previewResult;
    }

    /**
     * @return ImportRun
     */
    public function run(Import $import)
    {
        $importRun = new ImportRun();

        $this->eventDispatcher->dispatch(ImportEvent::COMPILE, new ImportEvent($importRun));

        $workflow = $this->buildWorkflow($import, $importRun);
        $workflow->process();
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildBaseWorkflow(Import $import)
    {
        //input
        $workflow = new Workflow($import->getSourceStorage()->reader());

        //validation
        $import
            ->applyValidation($workflow)
            ->applyMapping($workflow, $import->importer()->converters());

        return $workflow;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildPreviewWorkflow(Import $import, array &$previewResult, $offset = 0)
    {
        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //callback filter for getting the source-data
        $workflow->addFilter(new PriorityCallbackFilter(function (array $item) use (&$previewResult) {
            $previewResult["from"] = $item;

            return true;
        }, 512)); //before validation

        //output
        $workflow->addWriter(new ArrayWriter($previewResult["to"]));

        //event-hook
        $workflow->addFilter(new OffsetFilter($offset, 1));

        return $workflow;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildWorkflow(Import $import, ImportRun $importRun)
    {
        $importEventDispatcher = new ImportEventDispatcher($this->eventDispatcher, $importRun);

        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //output
        $workflow->addWriter($import->getTargetStorage()->writer());

        //event-hook
        $workflow
            ->addFilter($importEventDispatcher->afterReadFilter())
            ->addFilterAfterConversion($importEventDispatcher->afterConversionFilter())
            ->addWriter($importEventDispatcher);

        return $workflow;
    }

}
