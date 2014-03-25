<?php
namespace DataImportEngine\Mapping;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class Mapping implements \IteratorAggregate
{

    private $mappings = array();

    public function addMapping($from, $to, $converter=null)
    {
        $this->mappings[$from] = array('from'=>$from, 'to'=>$to, 'converter'=>$converter);
    }

    public function getMapping($from)
    {
        if (!array_key_exists($from, $this->mappings)) {
            return null;
        }

        return $this->mappings[$from];
    }

    public function converters()
    {
        return array();
    }

    public function apply(Workflow $workflow)
    {
        $fieldMapping = array();
        $converters = array();

        foreach ($this->mappings as $mapping) {
            extract($mapping);
            if (!empty($to)) {
                $fieldMapping[$from] = $to;
            }

            if ($converter instanceof ValueConverterInterface) {
                $workflow->addValueConverter($to, $converter);
            } elseif ($converter instanceof ItemConverterInterface) {
                $workflow->addItemConverter($converter);
            }
        }

        $workflow->addItemConverter(new MappingItemConverter($fieldMapping));
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->mappings);
    }

}
