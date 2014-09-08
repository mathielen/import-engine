<?php
namespace Mathielen\ImportEngine\Filter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;
use Ddeboer\DataImport\Workflow;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class Filters extends \ArrayObject
{

    /**
     * @return \Mathielen\ImportEngine\Filter\Filters
     */
    public function add($filter)
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
