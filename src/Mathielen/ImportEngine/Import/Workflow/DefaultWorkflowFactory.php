<?php
namespace Mathielen\ImportEngine\Import\Workflow;

use Mathielen\DataImport\EventDispatchableWorkflow;
use Mathielen\ImportEngine\Import\Import;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Mathielen\DataImport\Filter\PriorityCallbackFilter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mathielen\ImportEngine\Import\Run\Statistics\ImportRunStatisticsEventSubscriber;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class DefaultWorkflowFactory implements WorkflowFactoryInterface
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher=null)
    {
        if (!$eventDispatcher) {
            $eventDispatcher = new EventDispatcher();
        }

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \Ddeboer\DataImport\Workflow
     */
    private function buildBaseWorkflow(Import $import)
    {
        //input
        $workflow = new EventDispatchableWorkflow($import->getSourceStorage()->reader());
        $workflow->setEventDispatcher($this->eventDispatcher);

        //validation
        $import->importer()->filters()->apply($workflow);
        $import->mappings()->apply($workflow, $import->importer()->transformation()->converterProvider()->getAll());
        $import->importer()->validation()->apply($workflow);

        return $workflow;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface::buildPreviewWorkflow()
     */
    public function buildPreviewWorkflow(Import $import, array &$previewResult, $offset = 0)
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
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface::buildRunWorkflow()
     */
    public function buildRunWorkflow(Import $import, ImportRun $importRun)
    {
        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //output
        $workflow->addWriter($import->targetStorage()->writer());

        //collect statistics by default
        $statisticsCollector = new ImportRunStatisticsEventSubscriber($importRun);
        $this->eventDispatcher->addSubscriber($statisticsCollector);

        return $workflow;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface::buildDryrunWorkflow()
     */
    public function buildDryrunWorkflow(Import $import, ImportRun $importRun)
    {
        //build basics
        $workflow = $this->buildBaseWorkflow($import);

        //collect statistics by default
        $statisticsCollector = new ImportRunStatisticsEventSubscriber($importRun);
        $this->eventDispatcher->addSubscriber($statisticsCollector);

        return $workflow;
    }

}
