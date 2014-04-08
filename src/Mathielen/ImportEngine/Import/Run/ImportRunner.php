<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\DataImport\Workflow;
use Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface;

class ImportRunner
{

    /**
     * @var WorkflowFactoryInterface
     */
    private $workflowFactory;

    public function __construct(WorkflowFactoryInterface $workflowFactory = null)
    {
        $this->workflowFactory = $workflowFactory;
    }

    /**
     * @return array
     */
    public function preview(Import $import, $offset = 0)
    {
        $previewResult = array('from'=>array(), 'to'=>array());

        $workflow = $this->workflowFactory->buildPreviewWorkflow($import, $previewResult, $offset);
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

        $workflow = $this->workflowFactory->buildDryrunWorkflow($import, $importRun);
        $workflow->process();

        return $importRun;
    }

    /**
     * @return ImportRun
     */
    public function run(Import $import)
    {
        $importRun = new ImportRun(uniqid());

        $workflow = $this->workflowFactory->buildRunWorkflow($import, $importRun);
        $workflow->process();

        return $importRun;
    }

}
