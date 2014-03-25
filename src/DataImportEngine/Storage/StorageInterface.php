<?php
namespace DataImportEngine\Storage;

use Ddeboer\DataImport\Reader\ReaderInterface;
use Ddeboer\DataImport\Writer\WriterInterface;
use DataImportEngine\Storage\Type\Type;

interface StorageInterface
{

    /**
     * @return ReaderInterface
     */
    public function reader();

    /**
     * @return WriterInterface
     */
    public function writer();

    public function info();
    public function getFields();

    /**
     * @return Type
     */
    public function getType();

}
