<?php

namespace Mathielen\ImportEngine\Import\Workflow;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\DataImport\Workflow;
use Mathielen\ImportEngine\ValueObject\ImportRun;

interface WorkflowFactoryInterface
{
    /**
     * @return Workflow
     */
    public function buildPreviewWorkflow(Import $import, array &$previewResult, $offset = 0);

    /**
     * @return Workflow
     */
    public function buildDryrunWorkflow(Import $import, ImportRun $importRun);

    /**
     * @return Workflow
     */
    public function buildRunWorkflow(Import $import, ImportRun $importRun);
}
