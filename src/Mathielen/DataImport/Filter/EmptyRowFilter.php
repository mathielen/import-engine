<?php

namespace Mathielen\DataImport\Filter;

use Ddeboer\DataImport\Filter\FilterInterface;

class EmptyRowFilter implements FilterInterface
{
    /**
     * Filter input.
     *
     * @param array $item Input
     *
     * @return bool If false is returned, the workflow will skip the input
     */
    public function filter(array $item)
    {
        return count(array_filter($item)) > 0; //skip any empty rows
    }

    /**
     * Get filter priority (higher number means higher priority).
     *
     * @return int
     */
    public function getPriority()
    {
        return 1; //more then default
    }
}
