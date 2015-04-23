<?php
namespace Mathielen\ImportEngine\Import\Run;

use Ddeboer\DataImport\Workflow;
use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;
use Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface;
use Mathielen\ImportEngine\Exception\ImportRunException;
use Mathielen\ImportEngine\ValueObject\ImportRun;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportRunner
{

    /**
     * @var WorkflowFactoryInterface
     */
    private $workflowFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(WorkflowFactoryInterface $workflowFactory=null, EventDispatcherInterface $eventDispatcher=null)
    {
        if (!$workflowFactory) {
            $workflowFactory = new DefaultWorkflowFactory();
        }

        $this->workflowFactory = $workflowFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ImportRunner
     */
    public static function build(WorkflowFactoryInterface $workflowFactory=null)
    {
        return new self($workflowFactory);
    }

    private function process(Workflow $workflow, ImportRun $importRun=null)
    {
        if ($this->eventDispatcher && $importRun) {
            $e = new ImportProcessEvent($importRun);
            $this->eventDispatcher->dispatch(ImportProcessEvent::AFTER_PREPARE.'.'.$importRun->getConfiguration()->getImporterId(), $e);
        }

        $workflow->process();

        if ($this->eventDispatcher && $importRun) {
            $this->eventDispatcher->dispatch(ImportProcessEvent::AFTER_FINISH.'.'.$importRun->getConfiguration()->getImporterId(), $e);
        }
    }

    /**
     * @return array
     */
    public function preview(Import $import, $offset = 0)
    {
        $importRun = $import->getRun();
        $previewResult = array('from'=>array(), 'to'=>array());

        $workflow = $this->workflowFactory->buildPreviewWorkflow($import, $previewResult, $offset);
        $this->process($workflow, $importRun);

        if (0 == count($previewResult['from'])) {
            throw new ImportRunException("Unable to preview row with offset '$offset'. EOF?", $importRun);
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
     * @return Import
     */
    public function dryRun(Import $import)
    {
        $importRun = $import->getRun();
        $workflow = $this->workflowFactory->buildDryrunWorkflow($import, $importRun);
        $this->process($workflow, $importRun);

        return $importRun;
    }

    /**
     * @return Import
     */
    public function run(Import $import)
    {
        $importRun = $import->getRun();
        $workflow = $this->workflowFactory->buildRunWorkflow($import, $importRun);
        $this->process($workflow, $importRun);

        return $importRun;
    }

}
