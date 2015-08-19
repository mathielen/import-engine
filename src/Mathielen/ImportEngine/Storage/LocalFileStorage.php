<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\CsvReader;
use Mathielen\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Reader\ReaderInterface;
use Mathielen\DataImport\Writer\CsvWriter;
use Mathielen\DataImport\Writer\ExcelWriter;
use Mathielen\DataImport\Writer\XmlWriter;
use Ddeboer\DataImport\Writer\WriterInterface;
use Mathielen\DataImport\Reader\XmlReader;
use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\JsonFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class LocalFileStorage implements StorageFormatInterface, RecognizableStorageInterface
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

    /**
     * @var StorageInfo
     */
    private $info;

    public function __construct(\SplFileInfo $file, Format $format)
    {
        $this->file = $file;
        $this->format = $format;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return Format
     */
    public function getFormat()
    {
        return $this->format;
    }

    public function getAvailableFormats()
    {
        return array(
            'csv',
            'excel',
            'xml',
            'json',
            'zlib'
        );
    }

    public function reader()
    {
        if (!$this->reader) {
            if (!$this->isReadable()) {
                throw new InvalidConfigurationException('Cannot read from file '.$this->file);
            }

            $this->reader = $this->formatToReader($this->format, $this->file);
        }

        return $this->reader;
    }

    public function isReadable()
    {
        return $this->file->isReadable() && $this->file->getSize() > 0;
    }

    public function isWritable()
    {
        return is_writable(dirname($this->file));
    }

    private function formatToReader(Format $format, \SplFileInfo $file)
    {
        if ($format instanceof CsvFormat) {
            $reader = new CsvReader($file->openFile(), $format->getDelimiter(), $format->getEnclosure(), $format->getEscape());
            $reader->setStrict(false);
            if ($format->isHeaderInFirstRow()) {
                $reader->setHeaderRowNumber(0, CsvReader::DUPLICATE_HEADERS_MERGE);
                $reader->setColumnHeaders(array_map('trim', $reader->getColumnHeaders())); //TODO some header-collaborator?
            }

        } elseif ($format instanceof ExcelFormat) {
            $headerRowNumber = $format->isHeaderInFirstRow()?0:null;
            $reader = new ExcelReader($file->openFile(), $headerRowNumber, $format->getActivesheet());
            if ($format->isHeaderInFirstRow()) {
                $reader->setColumnHeaders(array_map('trim', $reader->getColumnHeaders())); //TODO some header-collaborator?
            }

        } elseif ($format instanceof JsonFormat) {
            $array = json_decode(file_get_contents($file), true);
            $reader = new ArrayReader($array);

        } elseif ($format instanceof XmlFormat) {
            $reader = new XmlReader($file->openFile(), $format->getXpath());

        } elseif ($format instanceof CompressedFormat && $format->getSubFormat()) {
            $reader = $this->formatToReader($format->getSubFormat(), $format->getInsideStream($file));

        } else {
            throw new InvalidConfigurationException("Cannot build reader. Unknown format: ".$format);
        }

        return $reader;
    }

    public function getFields()
    {
        if (!$this->isReadable()) {
            throw new InvalidConfigurationException("Cannot read from file ".$this->file);
        }

        return $this->reader()->getFields();
    }

    public function getHash()
    {
        if (!$this->isReadable()) {
            throw new InvalidConfigurationException("Cannot read from file ".$this->file);
        }

        return md5_file($this->file->getRealPath());
    }

    public function writer()
    {
        if (!$this->writer) {
            if (!$this->isWritable()) {
                throw new InvalidConfigurationException('Cannot write to file '.$this->file);
            }

            $this->writer = $this->formatToWriter($this->format, $this->file);
        }

        return $this->writer;
    }

    private function formatToWriter(Format $format, \SplFileInfo $file)
    {
        if ($format instanceof CsvFormat) {
            $writer = new CsvWriter($format->getDelimiter(), $format->getEnclosure(), fopen($file, 'a'), false, $format->isHeaderInFirstRow());

        } elseif ($format instanceof ExcelFormat) {
            $writer = new ExcelWriter($file->openFile('a'), $format->getActivesheet(), $format->getExceltype(), $format->isHeaderInFirstRow());

        } elseif ($format instanceof XmlFormat) {
            $writer = new XMLWriter($file->openFile('a'));

        } elseif ($format instanceof CompressedFormat) {
            throw new \LogicException("Not implemented!");

        } else {
            throw new InvalidConfigurationException("Cannot build writer. Unknown format: ".$format);
        }

        return $writer;
    }

    public function info()
    {
        if (!isset($this->info)) {
            $this->info = new StorageInfo(array(
                'name' => $this->file->getFilename(),
                'hash' => $this->isReadable()?$this->getHash():null,
                'format' => $this->getFormat(),
                'size' => $this->isReadable()?$this->file->getSize():0,
                'count' => $this->isReadable()?count($this->reader()):0
            ));
        }

        return $this->info;
    }

}
