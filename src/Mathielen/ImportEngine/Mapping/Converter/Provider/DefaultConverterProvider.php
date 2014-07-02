<?php
namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;
use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;

class DefaultConverterProvider implements ConverterProviderInterface
{

    private $converters;

    public function __construct()
    {
        $this->converters = array();

        $this->add('upperCase', new CallbackValueConverter(function ($item) {
            return strtoupper($item);
        }));
        $this->add('lowerCase', new CallbackValueConverter(function ($item) {
            return strtolower($item);
        }));
    }

    /**
     * @return \Mathielen\ImportEngine\Mapping\Converter\Provider\DefaultConverterProvider
     */
    public function add($id, $converter)
    {
        if (!($converter instanceof ValueConverterInterface || $converter instanceof ItemConverterInterface)) {
            throw new \InvalidArgumentException("Convert must implement ValueConverterInterface or ItemConverterInterface");
        }
        if (empty($id)) {
            throw new \InvalidArgumentException("Id cannot be empty");
        }

        $this->converters[$id] = $converter;

        return $this;
    }

    public function getAll()
    {
        return $this->converters;
    }

    public function get($id)
    {
        return $this->converters[$id];
    }

}
