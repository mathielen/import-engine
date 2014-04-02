<?php
namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\Reader\ReaderInterface;

class DefaultMappingFactory implements MappingFactoryInterface
{

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Mapping\MappingFactoryInterface::factor()
     */
    public function factor(ReaderInterface $reader)
    {
        $mapping = new Mappings();

        //foreach ($reader->getFields() as $field) {
            //$mapping->addMapping($field, 'to', 'convert');
        //}
        return $mapping;
    }

}
