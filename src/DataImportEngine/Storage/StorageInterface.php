<?php
namespace DataImportEngine\Storage;

use Ddeboer\DataImport\Reader\ReaderInterface;
use Ddeboer\DataImport\Writer\WriterInterface;

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

}
