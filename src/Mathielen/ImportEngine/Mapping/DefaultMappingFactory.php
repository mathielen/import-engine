<?php

namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\Reader\ReaderInterface;

class DefaultMappingFactory implements MappingFactoryInterface
{
    /**
     * @var Mappings
     */
    private $mappings;

    public function __construct(Mappings $mappings = null)
    {
        if (is_null($mappings)) {
            $mappings = new Mappings();
        }

        $this->mappings = $mappings;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Mapping\MappingFactoryInterface::factor()
     */
    public function factor(ReaderInterface $reader)
    {
        return $this->mappings;
    }
}
