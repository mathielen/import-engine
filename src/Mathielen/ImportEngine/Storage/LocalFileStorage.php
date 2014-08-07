<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Reader\ReaderInterface;
use Ddeboer\DataImport\Writer\CsvWriter;
use Ddeboer\DataImport\Writer\WriterInterface;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\ZipFormat;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class LocalFileStorage implements StorageFormatInterface
{

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var Format
     */
    private $format;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var WriterInterface
     */
    private $writer;

    public function __construct(\SplFileInfo $file, Format $format)
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
        if (!$this->reader) {
            $this->reader = $this->formatToReader($this->format, $this->file);
        }

        return $this->reader;
    }

    private function formatToReader($format, \SplFileInfo $file)
    {
        $reader = null;

        if ($format instanceof CsvFormat) {
            $reader = new CsvReader($file->openFile(), $format->delimiter, $format->enclosure, $format->escape);
            $reader->setStrict(false);
            if ($format->headerinfirstrow) {
                $reader->setHeaderRowNumber(0);
            }

        } elseif ($format instanceof ExcelFormat) {
            $headerRowNumber = $format->headerinfirstrow?0:null;
            $reader = new ExcelReader($file->openFile(), $headerRowNumber, $format->activesheet);

        } elseif ($format instanceof ZipFormat && $format->getSubFormat()) {
            file_put_contents('/tmp/unpacked', file_get_contents($format->getStreamUri()));

            $reader = $this->formatToReader($format->getSubFormat(), new \SplFileObject('/tmp/unpacked'));

        } else {
            throw new InvalidConfigurationException("Cannot build reader. Unknown format: ".$format);
        }

        return $reader;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {
        if (!$this->writer) {
           $this->writer = $this->formatToWriter($this->format, $this->file);
        }

        return $this->writer;
    }

    private function formatToWriter($format, \SplFileInfo $file)
    {
        $reader = null;

        if ($format instanceof CsvFormat) {
            $reader = new CsvWriter($format->delimiter, $format->enclosure, fopen($file, 'w'));
            if ($format->headerinfirstrow) {

            }

        } elseif ($format instanceof ExcelFormat) {
           // $headerRowNumber = $format->headerinfirstrow?0:null;
            //$reader = new ExcelReader($file->openFile(), $headerRowNumber, $format->activesheet);

        } elseif ($format instanceof ZipFormat && $format->getSubFormat()) {
            //file_put_contents('/tmp/unpacked', file_get_contents($format->getStreamUri()));

            //$reader = $this->formatToReader($format->getSubFormat(), new \SplFileObject('/tmp/unpacked'));

        } else {
            throw new InvalidConfigurationException("Cannot build writer. Unknown format: ".$format);
        }

        return $reader;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return new StorageInfo(array(
            'name' => $this->file->getFilename(),
            'hash' => md5_file($this->file->getRealPath()),
            'format' => $this->getFormat(),
            'size' => $this->file->getSize(),
            'count' => count($this->reader())
        ));
    }

}
