<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;
use Mathielen\ImportEngine\Import\Workflow\WorkflowFactoryInterface;
use Mathielen\ImportEngine\Exception\ImportRunException;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class ImportRunner
{

    /**
     * @var WorkflowFactoryInterface
     */
    private $workflowFactory;

    public function __construct(WorkflowFactoryInterface $workflowFactory=null)
    {
        if (!$workflowFactory) {
            $workflowFactory = new DefaultWorkflowFactory();
        }

        $this->workflowFactory = $workflowFactory;
    }

    /**
     * @return ImportRunner
     */
    public static function build(WorkflowFactoryInterface $workflowFactory=null)
    {
        return new self($workflowFactory);
    }

    /**
     * @return array
     */
    public function preview(Import $import, $offset = 0)
    {
        $importRun = $import->getRun();
        $previewResult = array('from'=>array(), 'to'=>array());

        $workflow = $this->workflowFactory->buildPreviewWorkflow($import, $previewResult, $offset);
        $workflow->process();

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
        $workflow->process();

        return $importRun;
    }

    /**
     * @return Import
     */
    public function run(Import $import)
    {
        $importRun = $import->getRun();
        $workflow = $this->workflowFactory->buildRunWorkflow($import, $importRun);
        $workflow->process();

        return $importRun;
    }

}
