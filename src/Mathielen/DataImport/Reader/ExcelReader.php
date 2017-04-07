<?php

namespace Mathielen\DataImport\Reader;

class ExcelReader extends \Ddeboer\DataImport\Reader\ExcelReader
{
    private $activeSheet;
    private $file;

    /**
     * @var \PHPExcel_Reader_IReader
     */
    private $reader;
    private $maxRows = null;
    private $maxCol = null;

    public function __construct(\SplFileObject $file, $headerRowNumber = null, $activeSheet = null, $readOnly = true, $maxRows = null, $maxCol = null)
    {
        $this->file = $file;
        $this->activeSheet = $activeSheet;

        $this->reader = \PHPExcel_IOFactory::createReaderForFile($file->getPathName());
        $this->reader->setReadDataOnly($readOnly);

        if (!is_null($headerRowNumber)) {
            $this->headerRowNumber = $headerRowNumber;
            $headerReader = clone $this->reader;
            $headerReader->setReadFilter(new ReadFilter($headerRowNumber + 1));

            /** @var \PHPExcel $excel */
            $excel = $headerReader->load($file->getPathname());

            if (null !== $activeSheet) {
                $excel->setActiveSheetIndex($activeSheet);
            }

            $rows = $excel->getActiveSheet()->toArray();
            $this->columnHeaders = $rows[$headerRowNumber];

            //set max col from header length if not already given
            if (is_null($maxCol)) {
                $maxCol = \PHPExcel_Cell::stringFromColumnIndex(count($this->columnHeaders) - 1);
            }
        }

        $this->setBoundaries($maxRows, $maxCol);
    }

    public function setBoundaries($maxRows = null, $maxCol = null)
    {
        $this->setMaxRows($maxRows);
        $this->setMaxCol($maxCol);
    }

    public function setMaxRows($maxRows = null)
    {
        $this->maxRows = $maxRows;
    }

    public function setMaxCol($maxCol = null)
    {
        $this->maxCol = $maxCol;
    }

    public function count()
    {
        if (is_null($this->count)) {
            $countReader = clone $this->reader;
            $countReader->setReadFilter(new ReadFilter($this->maxRows, 'A'));

            /** @var \PHPExcel $excel */
            $excel = $countReader->load($this->file->getPathname());

            if (null !== $this->activeSheet) {
                $excel->setActiveSheetIndex($this->activeSheet);
            }

            $maxRowMaxCol = $excel->getActiveSheet()->getHighestRowAndColumn();
            $this->count = $maxRowMaxCol['row'];

            if (null !== $this->headerRowNumber) {
                --$this->count;
            }
        }

        return $this->count;
    }

    public function rewind()
    {
        if (is_null($this->worksheet)) {
            $this->reader->setReadFilter(new ReadFilter($this->maxRows, $this->maxCol));

            /** @var \PHPExcel $excel */
            $excel = $this->reader->load($this->file->getPathname());

            if (null !== $this->activeSheet) {
                $excel->setActiveSheetIndex($this->activeSheet);
            }

            $this->worksheet = $excel->getActiveSheet()->toArray();
        }

        parent::rewind();
    }

    public function valid()
    {
        return parent::valid() && $this->pointer <= $this->count();
    }

}

class ReadFilter implements \PHPExcel_Reader_IReadFilter
{
    private $maxRows;
    private $maxColIdx;

    public function __construct($maxRows = null, $maxCol = null)
    {
        $this->maxRows = $maxRows;
        $this->maxColIdx = $maxCol ? \PHPExcel_Cell::columnIndexFromString($maxCol) : null;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if (isset($this->maxRows) && $row > $this->maxRows) {
            return false;
        }
        if (isset($this->maxColIdx) && \PHPExcel_Cell::columnIndexFromString($column) > $this->maxColIdx) {
            return false;
        }

        return true;
    }
}
