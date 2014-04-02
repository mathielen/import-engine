<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Mathielen\ImportEngine\Storage\Type\Type;
use Mathielen\ImportEngine\Storage\Type\CsvType;
use Mathielen\ImportEngine\Storage\Type\ExcelType;
use Mathielen\ImportEngine\Storage\Type\ZipType;

class LocalFileStorage implements StorageSubtypeInterface
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
     * @see \Mathielen\ImportEngine\Storage\StorageSubtypeInterface::getType()
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\StorageSubtypeInterface::availableTypes()
     */
    public function getAvailableTypes()
    {
        return array(
            'csv',
            'excel',
            'xml',
            'zip'
        );
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
//            $reader = new CompressedStreamReader($this->typeToReader($type->getSubType()));

        }

        return $reader;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {

    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\SourceInterface::info()
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
