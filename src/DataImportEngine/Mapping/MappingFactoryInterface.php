<?php
namespace DataImportEngine\Mapping;

use Ddeboer\DataImport\Reader\ReaderInterface;
interface MappingFactoryInterface
{

    /**
     * @return Mappings
     */
    public function factor(ReaderInterface $reader);

}
