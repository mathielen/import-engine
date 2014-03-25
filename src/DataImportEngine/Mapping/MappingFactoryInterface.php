<?php
namespace DataImportEngine\Mapping;

use Ddeboer\DataImport\Reader\ReaderInterface;
interface MappingFactoryInterface
{

    /**
     * @return Mapping
     */
    public function factor(ReaderInterface $reader);

}
