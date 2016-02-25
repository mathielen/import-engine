<?php
namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class UnknownPropertiesItemConverter implements ItemConverterInterface
{

    private $knownProperties;

    private $targetProperty;

    private $skipEmptyKey;

    public function __construct(array $knownProperties, $targetProperty='ATTRIBUTES', $skipEmptyKey=true)
    {
        $this->knownProperties = $knownProperties;
        $this->targetProperty = $targetProperty;
        $this->skipEmptyKey = $skipEmptyKey;

        $this->knownProperties[] = $this->targetProperty;
    }

    public static function fromClass($cls, $targetProperty='ATTRIBUTES', $skipEmptyKey=true)
    {
        $r = new \ReflectionClass($cls);
        $properties = $r->getProperties();
        $properties = array_map(function (\ReflectionProperty $e) {
            return $e->getName();
        }, $properties);

        return new self($properties, $targetProperty, $skipEmptyKey);
    }

    public function convert($input)
    {
        $unknownProperties = array_udiff(array_keys($input), $this->knownProperties, 'strcasecmp');

        //has unknown properties
        if (count($unknownProperties) > 0) {
            //target property does not exist => make it an array
            if (!isset($input[$this->targetProperty])) {
                $input[$this->targetProperty] = [];
            }

            //copy unknown properties to target property and remove them from input
            foreach ($unknownProperties as $property) {
                if (!$this->skipEmptyKey || !empty($property)) {
                    $input[$this->targetProperty][$property] = $input[$property];
                }
                unset($input[$property]);
            }
        }

        return $input;
    }

}
