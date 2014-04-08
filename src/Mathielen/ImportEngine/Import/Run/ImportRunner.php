<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\DataImport\Workflow;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Mathielen\DataImport\Filter\PriorityCallbackFilter;
use Mathielen\ImportEngine\Import\Run\Statistics\ImportRunStatisticsEventSubscriber;

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

        $statisticsCollector = new ImportRunStatisticsEventSubscriber();
        $this->eventDispatcher->addSubscriber($statisticsCollector);

        $workflow = $this->buildBaseWorkflow($import, $importRun);
        $workflow->process();

        $importRun->setStatistics($statisticsCollector->getStatistics());

        return $importRun;
    }

    /**
     * @return ImportRun
     */
    public function run(Import $import)
    {
        $importRun = new ImportRun(uniqid());

        $workflow = $this->buildRunWorkflow($import, $importRun);
        $workflow->process();

        return $importRun;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildBaseWorkflow(Import $import)
    {
        //input
        $workflow = new Workflow($import->getSourceStorage()->reader());
        $workflow->setEventDispatcher($this->eventDispatcher);

        //validation
        $import
            ->applyValidation($workflow)
            ->applyMapping($workflow, $import->importer()->converters());

        return $workflow;
    }

    /**
     * does not actually write to target
     * has specific filters and writers for preview
     *
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
    private function buildRunWorkflow(Import $import, ImportRun $importRun)
    {
        //build basics
        $workflow = $this->buildBaseWorkflow($import, $importRun);

        //output
        $workflow->addWriter($import->getTargetStorage()->writer());

        return $workflow;
    }

}
