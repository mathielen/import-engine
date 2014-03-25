<?php
namespace DataImportEngine\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class ObjectReader implements ReaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        // Examine first row
        if ($this->count() > 0) {
            return \array_keys($this[0]);
        }

        return array();
    }
}
