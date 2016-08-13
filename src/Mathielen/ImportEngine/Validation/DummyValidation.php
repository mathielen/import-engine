<?php

namespace Mathielen\ImportEngine\Validation;

use Ddeboer\DataImport\Workflow;

class DummyValidation implements ValidationInterface
{
    public function apply(Workflow $workflow)
    {
        //do nothing
    }

    public function getViolations()
    {
        return array();
    }
}
