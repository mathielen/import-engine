<?php

namespace Mathielen\ImportEngine\Filter;

use Ddeboer\DataImport\Filter\FilterInterface;
use Ddeboer\DataImport\Workflow;

class Filters extends \ArrayObject
{
    /**
     * @return \Mathielen\ImportEngine\Filter\Filters
     */
    public function add(FilterInterface $filter)
    {
        $this->append($filter);

        return $this;
    }

    public function apply(Workflow $workflow)
    {
        foreach ($this as $filter) {
            $workflow->addFilter($filter);
        }
    }
}
