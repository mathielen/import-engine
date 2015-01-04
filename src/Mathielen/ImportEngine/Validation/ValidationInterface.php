<?php
namespace Mathielen\ImportEngine\Validation;

use Ddeboer\DataImport\Workflow;

interface ValidationInterface
{

    /**
     * @return ValidationInterface
     */
    public function apply(Workflow $workflow);

    /**
     * @return array
     */
    public function getViolations();

}
