<?php
namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class Mappings extends \ArrayObject
{

    public function getTargetFields()
    {
        $fields = array();
        foreach ($this as $row) {
            $fields[] = $row->to;
        }

        return $fields;
    }

    /**
     * @return \Mathielen\ImportEngine\Mapping\Mapping
     */
    public function add($from, $to, $converter=null)
    {
        $this->getOrCreateMapping($from)->to = $to;

        if ($converter) {
            $this->setConverter($from, $converter);
        }

        return $this;
    }

    public function setConverter($from, $converter)
    {
        $this->getOrCreateMapping($from)->converter = $converter;
    }

    /**
     * @return \Mathielen\ImportEngine\Mapping\Mapping
     */
    private function getOrCreateMapping($from)
    {
        if (!array_key_exists($from, $this)) {
            $this[$from] = new Mapping($from);
        }

        return $this[$from];
    }

    public function get($from)
    {
        if (!array_key_exists($from, $this)) {
            return null;
        }

        return $this[$from];
    }

    public function apply(Workflow $workflow, array $converters)
    {
        $fieldMapping = array();

        foreach ($this as $mapping) {
            $to = $mapping->to;
            $from = $mapping->from;
            $converter = $mapping->converter;

            if (!empty($to)) {
                $fieldMapping[$from] = $to;
            }

            if ($converter && array_key_exists($converter, $converters)) {
                $converter = $converters[$converter];
                if ($converter instanceof ValueConverterInterface) {
                    $workflow->addValueConverter($to, $converter);
                } elseif ($converter instanceof ItemConverterInterface) {
                    $workflow->addItemConverter($converter);
                }
            }
        }

        $workflow->addItemConverter(new MappingItemConverter($fieldMapping));
    }

}
