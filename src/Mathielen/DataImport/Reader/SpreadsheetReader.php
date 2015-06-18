<?php
namespace Mathielen\DataImport\Reader;

use Ddeboer\DataImport\Reader\CountableReaderInterface;

/**
 * Reads Excel files with the help of Spreadsheet-Reader
 *
 * spreadsheet-reader must be installed.
 */
class SpreadsheetReader implements CountableReaderInterface, \SeekableIterator
{

    /**
     * @var \SpreadsheetReader
     */
    protected $reader;

    protected $headerRowNumber;
    protected $columnHeaders;

    /**
     * Construct CSV reader
     *
     * @param \SplFileObject $file            Excel file
     * @param int            $headerRowNumber Optional number of header row
     * @param int            $activeSheet     Index of active sheet to read from
     */
    public function __construct(\SplFileObject $file, $headerRowNumber = null, $activeSheet = null)
    {
        $this->reader = new \SpreadsheetReader($file->getRealPath());

        if (null !== $activeSheet) {
            $this->reader->ChangeSheet($activeSheet);
        }

        if (null !== $headerRowNumber) {
            $this->setHeaderRowNumber($headerRowNumber);
        }
    }

    /**
     * Return the current row as an array
     *
     * If a header row has been set, an associative array will be returned
     *
     * @return array
     */
    public function current()
    {
        $row = $this->reader->current();

        // If the CSV has column headers, use them to construct an associative
        // array for the columns in this line
        if (!empty($this->columnHeaders)) {
            // Count the number of elements in both: they must be equal.
            // If not, ignore the row
            if (count($this->columnHeaders) == count($row)) {
                return array_combine(array_values($this->columnHeaders), $row);
            }
        } else {
            // Else just return the column values
            return $row;
        }
    }

    /**
     * Get column headers
     *
     * @return array
     */
    public function getColumnHeaders()
    {
        return $this->columnHeaders;
    }

    /**
     * Set column headers
     *
     * @param array $columnHeaders
     *
     * @return $this
     */
    public function setColumnHeaders(array $columnHeaders)
    {
        $this->columnHeaders = $columnHeaders;

        return $this;
    }

    /**
     * Rewind the file pointer
     *
     * If a header row has been set, the pointer is set just below the header
     * row. That way, when you iterate over the rows, that header row is
     * skipped.
     *
     */
    public function rewind()
    {
        if (null === $this->headerRowNumber) {
            $this->reader->rewind();
        } else {
            $this->reader->seek($this->headerRowNumber+1);
        }
    }

    /**
     * Set header row number
     *
     * @param int $rowNumber Number of the row that contains column header names
     *
     * @return $this
     */
    public function setHeaderRowNumber($rowNumber)
    {
        $this->headerRowNumber = $rowNumber;

        $this->reader->seek($rowNumber);
        $this->columnHeaders = $this->current();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->reader->next();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->reader->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->reader->key();
    }

    /**
     * {@inheritdoc}
     */
    public function seek($pointer)
    {
        $this->reader->seek($pointer);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $c = 0;
        $this->rewind();
        while ($this->valid()) {
            ++$c;
            $this->next();
        }

        return $c;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->columnHeaders;
    }

    /**
     * Get a row
     *
     * @param int $number Row number
     *
     * @return array
     */
    public function getRow($number)
    {
        $this->seek($number);

        return $this->current();
    }
}
