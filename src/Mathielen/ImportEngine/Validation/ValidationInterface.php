<?php
namespace Mathielen\ImportEngine\Validation;

use Ddeboer\DataImport\Workflow;

interface ValidationInterface
{

    public function apply(Workflow $workflow);

    public function getViolations();

}
