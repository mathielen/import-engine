<?php
namespace DataImportEngine\Import\Run;

use DataImportEngine\Import\Import;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DataImportEngine\Import\Event\ImportEvent;
use DataImportEngine\Eventing\ImportEventDispatcher;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Filter\CallbackFilter;

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

        //cleanup
        $previewResult['to'] = $previewResult['to'][0];

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
        $import->validation()->apply($workflow);

        //mapping
        $import->mappings()->apply($workflow, $import->importer()->converters());

        return $workflow;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildPreviewWorkflow(Import $import, array &$previewResult, $offset = 0)
    {
        //basics
        $workflow = $this->buildBaseWorkflow($import);

        $workflow->addFilter(new CallbackFilter(function (array $item) use (&$previewResult) {
            $previewResult["from"] = $item;

            return true;
        }));

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

        //basics
        $workflow = $this->buildBaseWorkflow($import);

        //output
        $workflow->addWriter($import->getTargetStorage()->writer());

        //event-hook
        $workflow->addFilter($importEventDispatcher->afterReadFilter());
        $workflow->addFilterAfterConversion($importEventDispatcher->afterConversionFilter());
        $workflow->addWriter($importEventDispatcher);

        return $workflow;
    }

}
