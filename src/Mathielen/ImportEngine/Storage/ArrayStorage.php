<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Writer\ArrayWriter;

class ArrayStorage implements StorageInterface
{

    private $array;

    public function __construct(array &$array)
    {
        $this->array = &$array;
    }

    public function getFields()
    {
        return $this->reader()->getFields();
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\SourceInterface::reader()
     */
    public function reader()
    {
        return new ArrayReader($this->array);
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {
        return new ArrayWriter($this->array);
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return new StorageInfo(array(
            'name' => 'Array Storage',
            'format' => 'Array Storage',
            'count' => count($this->reader())
        ));
    }

}
