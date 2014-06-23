<?php
namespace Mathielen\ImportEngine\Validation;

use Mathielen\DataImport\Workflow;

interface ValidationInterface
{

    public function apply(Workflow $workflow);

    public function getViolations();

}
