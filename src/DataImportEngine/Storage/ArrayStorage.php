<?php
namespace DataImportEngine\Storage;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Writer\ArrayWriter;

class ArrayStorage extends \SplObjectStorage implements StorageInterface
{

    private $array;

    public function __construct(&$array)
    {
        $this->array = $array;
    }

    public function getFields()
    {
        return $this->reader()->getFields();
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::reader()
     */
    public function reader()
    {
        return new ArrayReader($this->array);
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {
        return new ArrayWriter($this->array);
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return array(
            'name' => 'Array Storage'
        );
    }

    /**
     * @return array|null
     */
    public function preview($rowNum = 0)
    {
        $i = 0;
        foreach ($this->reader() as $row) {
            if ($i == $rowNum) {
                return $row;
            }
            ++$i;
        }
    }

}
