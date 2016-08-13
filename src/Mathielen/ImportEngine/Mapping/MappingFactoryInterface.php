<?php

namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\Reader\ReaderInterface;

interface MappingFactoryInterface
{
    /**
     * @return Mappings
     */
    public function factor(ReaderInterface $reader);
}
