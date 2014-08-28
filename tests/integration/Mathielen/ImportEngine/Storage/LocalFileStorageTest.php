<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;

class LocalFileStorageTest extends \PHPUnit_Framework_TestCase
{

    public function testCsv()
    {
        $format = new CsvFormat(';');
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../../metadata/testfiles/flatdata.csv'), $format);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(new StorageInfo(array(
            'name' => 'flatdata.csv',
            'format' => $format,
            'size' => 32,
            'count' => 1,
            'hash' => '4f0e1e99eb12d94dc527fedce9eb58d8'
        )), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }

    public function testXls()
    {
        $format = new ExcelFormat();
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../../metadata/testfiles/flatdata-excel.xls'), $format);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(new StorageInfo(array(
            'name' => 'flatdata-excel.xls',
            'format' => $format,
            'size' => 23552,
            'count' => 1,
            'hash' => '3dbea55520f59ebdd08b6ad85ae95005'
        )), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }

    public function testXlsx()
    {
        $format = new ExcelFormat();
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../../metadata/testfiles/flatdata-excel-xml.xlsx'), $format);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(new StorageInfo(array(
            'name' => 'flatdata-excel-xml.xlsx',
            'format' => $format,
            'size' => 8895,
            'count' => 2,
            'hash' => 'b297aa9bbc37f9cd51d8472498772474'
        )), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }
}
