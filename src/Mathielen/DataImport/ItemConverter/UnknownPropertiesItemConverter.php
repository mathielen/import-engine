<?php
namespace Mathielen\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

class UnknownPropertiesItemConverter implements ItemConverterInterface
{

    private $knownProperties;

    private $targetProperty;

    private $skipEmptyKey;

    public function __construct(array $knownProperties, $targetProperty = 'ATTRIBUTES', $skipEmptyKey = true)
    {
        $this->knownProperties = array_map("strtoupper", $knownProperties);
        $this->targetProperty = $targetProperty;
        $this->skipEmptyKey = $skipEmptyKey;

        if (!in_array(strtoupper($this->targetProperty), $this->knownProperties)) {
            $this->knownProperties[] = strtoupper($this->targetProperty);
        }
    }

    public static function fromClass($cls, $targetProperty = 'ATTRIBUTES', $skipEmptyKey = true)
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
        $currentKeys = array_keys($input);
        $unknownProperties = array_udiff($currentKeys, $this->knownProperties, function ($a, $b) {
            return strcasecmp(str_replace('_', '', $a), str_replace('_', '', $b)); //compare the keys, without _
        });

        //has unknown properties
        if (count($unknownProperties) > 0) {
            //target property does not exist => make it an array
            if (!isset($input[$this->targetProperty])) {
                $input[$this->targetProperty] = [];
            }

            //copy unknown properties to target property and remove them from input
            foreach ($unknownProperties as $property) {
                //if key is not empty and value of key is not empty => copy it to target property-array
                if (!$this->skipEmptyKey || !empty($property) && !empty($input[$property])) {
                    $input[$this->targetProperty][$property] = $input[$property];
                }
                unset($input[$property]);
            }
        }

        return $input;
    }

}
