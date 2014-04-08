<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\ZipFormat;

class LocalFileStorage implements StorageFormatInterface
{

    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var Format
     */
    private $format;

    public function __construct(\SplFileObject $file, Format $format)
    {
        $this->file = $file;
        $this->format = $format;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\StorageFormatInterface::getFormat()
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\StorageFormatInterface::getAvailableFormats()
     */
    public function getAvailableFormats()
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
        return $this->formatToReader($this->format);
    }

    private function formatToReader($format)
    {
        $reader = null;

        if ($format instanceof CsvFormat) {
            $reader = new CsvReader($this->file, $format->delimiter, $format->enclosure, $format->escape);
            $reader->setStrict(false);
            if ($format->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($format instanceof ExcelFormat) {
            $reader = new ExcelReader($this->file);
            if ($format->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($format instanceof ZipFormat) {
//            $reader = new CompressedStreamReader($this->typeToReader($type->getSubType()));

        } else {
            throw new \LogicException("Cannot build reader. Unknown format: ".$format);
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
            'format' => $this->getFormat(),
            'size' => $this->file->getSize(),
            'count' => count($this->reader())
        );
    }

}
