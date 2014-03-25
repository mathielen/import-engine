<?php
namespace DataImportEngine\Storage;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use DataImportEngine\Storage\Type\Type;
use DataImportEngine\Storage\Type\CsvType;
use DataImportEngine\Storage\Type\ExcelType;
use DataImportEngine\Storage\Type\ZipType;
use DataImportEngine\Reader\CompressedStreamReader;

class LocalFileStorage implements StorageInterface
{

    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var Type
     */
    private $type;

    public function __construct(\SplFileObject $file, Type $type)
    {
        $this->file = $file;
        $this->type = $type;
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Storage\StorageInterface::getType()
     */
    public function getType()
    {
        return $this->type;
    }

    public function getFields()
    {
        return $this->reader()->getFields();
    }

    public function availableTypes()
    {
        return array('csv', 'excel', 'xml', 'zip');
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::reader()
     */
    public function reader()
    {
        return $this->typeToReader($this->type);
    }

    private function typeToReader($type)
    {
        $reader = null;

        if ($type instanceof CsvType) {
            $reader = new CsvReader($this->file, $type->delimiter, $type->enclosure, $type->escape);
            $reader->setStrict(false);
            if ($type->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($type instanceof ExcelType) {
            $reader = new ExcelReader($this->file);
            if ($type->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($type instanceof ZipType) {
            $reader = new CompressedStreamReader($this->typeToReader($type->getSubType()));

        }

        return $reader;
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {

    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return array(
            'name' => $this->file->getFilename(),
            'type' => $this->getType(),
            'size' => $this->file->getSize(),
            'count' => count($this->reader())
        );
    }

}
