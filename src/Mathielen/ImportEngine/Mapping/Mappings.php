<?php
namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;
use Ddeboer\DataImport\Workflow;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\Mapping\Converter\Provider\ConverterProviderInterface;

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
     * @return Mappings
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
        if (!(is_string($converter) || $converter instanceof ValueConverterInterface || $converter instanceof ItemConverterInterface)) {
            throw new \InvalidArgumentException("Converter must be an id (string) or of type ValueConverterInterface or ItemConverterInterface");
        }

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
        if (!isset($this[$from])) {
            $this[$from] = new Mapping($from);
        }

        return $this[$from];
    }

    public function get($from)
    {
        if (!isset($this[$from])) {
            return null;
        }

        return $this[$from];
    }

    public function apply(Workflow $workflow, ConverterProviderInterface $converterProvider)
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
                if (is_string($converter)) {
                    if (!$converterProvider->has($converter)) {
                        throw new InvalidConfigurationException("Converter with id '$converter' not found in configured converters.");
                    }

                    $converter = $converterProvider->get($converter);
                }

                if ($converter instanceof ValueConverterInterface) {
                    $workflow->addValueConverter($from, $converter);
                } elseif ($converter instanceof ItemConverterInterface) {
                    $workflow->addItemConverter($converter);
                }
            }
        }

        if (!empty($fieldMapping)) {
            $workflow->addItemConverter(new MappingItemConverter($fieldMapping));
        }
    }

}
