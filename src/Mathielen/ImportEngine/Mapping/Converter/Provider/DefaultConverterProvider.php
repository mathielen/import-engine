<?php
namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;

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
