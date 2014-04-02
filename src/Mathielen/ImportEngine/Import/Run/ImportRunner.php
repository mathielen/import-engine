<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\Import;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Mathielen\ImportEngine\Import\Event\ImportEventDispatcher;
use Mathielen\ImportEngine\Import\Filter\PriorityCallbackFilter;

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

    /**
     * @return array
     */
    public function preview(Import $import, $offset = 0)
    {
        $previewResult = array('from'=>array(), 'to'=>array());

        $workflow = $this->buildPreviewWorkflow($import, $previewResult, $offset);
        $workflow->process();

        if (0 == count($previewResult['from'])) {
            throw new \LogicException("Unable to preview row with offset '$offset'. EOF?");
        }

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
    public function dryRun(Import $import)
    {
        $importRun = new ImportRun(uniqid());

        $workflow = $this->buildDryRunWorkflow($import, $importRun);
        $workflow->process();
    }

    /**
     * @return ImportRun
     */
    public function run(Import $import)
    {
        $importRun = new ImportRun(uniqid());

        $workflow = $this->buildRunWorkflow($import, $importRun);
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
        }, 96)); //before validation (64) but after offset (128)

        //output
        $workflow->addWriter(new ArrayWriter($previewResult["to"]));

        //preview offset
        $workflow->addFilter(new OffsetFilter($offset, 1));

        return $workflow;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildDryRunWorkflow(Import $import, ImportRun $importRun)
    {
        $importEventDispatcher = new ImportEventDispatcher($this->eventDispatcher, $importRun);

        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //event-hooks
        $workflow
            ->addFilter($importEventDispatcher->afterReadFilter())
            ->addFilterAfterConversion($importEventDispatcher->afterConversionFilter())
            ->addWriter($importEventDispatcher);

        return $workflow;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildRunWorkflow(Import $import, ImportRun $importRun)
    {
        $importEventDispatcher = new ImportEventDispatcher($this->eventDispatcher, $importRun);

        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //output
        $workflow->addWriter($import->getTargetStorage()->writer());

        //event-hooks
        $workflow
            ->addFilter($importEventDispatcher->afterReadFilter())
            ->addFilterAfterConversion($importEventDispatcher->afterConversionFilter())
            ->addWriter($importEventDispatcher);

        return $workflow;
    }

}
