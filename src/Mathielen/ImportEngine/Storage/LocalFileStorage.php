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
            'zlib'
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
        return $this->formatToReader($this->format, $this->file);
    }

    private function formatToReader($format, \SplFileObject $file)
    {
        $reader = null;

        if ($format instanceof CsvFormat) {
            $reader = new CsvReader($file, $format->delimiter, $format->enclosure, $format->escape);
            $reader->setStrict(false);
            if ($format->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($format instanceof ExcelFormat) {
            $reader = new ExcelReader($file);
            if ($format->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($format instanceof ZipFormat && $format->getSubFormat()) {
            file_put_contents('/tmp/unpacked', file_get_contents($format->getStreamUri()));

            $reader = $this->formatToReader($format->getSubFormat(), new \SplFileObject('/tmp/unpacked'));

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
