<?php
namespace DataImportEngine\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class IteratorReader implements ReaderInterface
{

    private $iterator;

    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

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
