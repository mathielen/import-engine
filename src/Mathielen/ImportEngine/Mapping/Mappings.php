<?php
namespace Mathielen\ImportEngine\Mapping;

use Mathielen\DataImport\Workflow;
use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

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
            $this->setConverter($converter, $from);
        }

        return $this;
    }

    public function setConverter($converter, $from=null)
    {
        if ($from) {
            $this->getOrCreateMapping($from)->converter = $converter;
        } else {
            $this->append($converter);
        }
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
            if ($mapping instanceof Mapping) {
                $to = $mapping->to;
                $from = $mapping->from;
                $converter = $mapping->converter;
            } else {
                $converter = $mapping;
            }

            if (!empty($to)) {
                $fieldMapping[$from] = $to;
            }

            if ($converter) {
                if (!array_key_exists($converter, $converters)) {
                    throw new InvalidConfigurationException("Converter with id '$converter' not found in configured converters.");
                }

                $converter = $converters[$converter];
                if ($converter instanceof ValueConverterInterface) {
                    $workflow->addValueConverter($from, $converter);

                } elseif ($converter instanceof ItemConverterInterface) {
                    $workflow->addItemConverter($converter);
                }
            }
        }

        $workflow->addItemConverter(new MappingItemConverter($fieldMapping));
    }

}
