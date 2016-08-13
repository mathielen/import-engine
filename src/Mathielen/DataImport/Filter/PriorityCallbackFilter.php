<?php

namespace Mathielen\DataImport\Filter;

use Ddeboer\DataImport\Filter\CallbackFilter;

class PriorityCallbackFilter extends CallbackFilter
{
    private $priority;

    public function __construct($callback, $priority = 0)
    {
        parent::__construct($callback);
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
