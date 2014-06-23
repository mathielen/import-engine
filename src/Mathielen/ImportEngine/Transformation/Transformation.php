<?php
namespace Mathielen\ImportEngine\Transformation;

use Mathielen\ImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use Mathielen\ImportEngine\Mapping\Converter\Provider\ConverterProviderInterface;
use Mathielen\ImportEngine\Mapping\MappingFactoryInterface;
use Mathielen\ImportEngine\Mapping\DefaultMappingFactory;
use Ddeboer\DataImport\Reader\ReaderInterface;

class Transformation
{

    /**
     * @var ConverterProviderInterface
     */
    private $converterProvider;

    /**
     * @var MappingFactoryInterface
     */
    private $mappingFactory;

    public function __construct()
    {
        $this->setConverterProvider(new DefaultConverterProvider());
        $this->setMappingFactory(new DefaultMappingFactory());
    }

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function setConverterProvider(ConverterProviderInterface $converterProvider)
    {
        $this->converterProvider = $converterProvider;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function setMappingFactory(MappingFactoryInterface $mappingFactory)
    {
        $this->mappingFactory = $mappingFactory;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Mapping\Converter\Provider\ConverterProviderInterface
     */
    public function converterProvider()
    {
        return $this->converterProvider;
    }

    /**
     * @return \Mathielen\ImportEngine\Mapping\Mappings
     */
    public function buildMapping(ReaderInterface $reader)
    {
        return $this->mappingFactory->factor($reader);
    }

}
