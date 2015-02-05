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
        $this->knownProperties = array_map('strtoupper', $knownProperties);
        $this->targetProperty = strtoupper($targetProperty);
        $this->skipEmptyKey = $skipEmptyKey;

        $this->knownProperties[] = $this->targetProperty;
    }

    public function convert($input)
    {
        $input = array_change_key_case($input, CASE_UPPER);
        $unknownProperties = array_diff(array_keys($input), $this->knownProperties);

        //has unknown properties
        if (count($unknownProperties) > 0) {
            //target property does not exist => make it an array
            if (!array_key_exists($this->targetProperty, $input)) {
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
