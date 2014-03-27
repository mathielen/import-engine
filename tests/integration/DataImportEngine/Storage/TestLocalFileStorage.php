<?php
namespace DataImportEngine\Storage;

use DataImportEngine\Storage\Type\CsvType;
use DataImportEngine\Storage\Type\ExcelType;

class TestLocalFileStorage extends \PHPUnit_Framework_TestCase
{

    public function testCsv()
    {
        $type = new CsvType('#');
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../metadata/testfiles/flatdata.csv'), $type);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
            'name' => 'flatdata.csv',
            'type' => $type,
            'size' => 2846,
            'count' => 1
        ), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(185, count($headers));
    }

    public function testXls()
    {
        $type = new ExcelType();
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../metadata/testfiles/flatdata-excel.xls'), $type);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
            'name' => 'flatdata-excel.xls',
            'type' => $type,
            'size' => 23552,
            'count' => 1
        ), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }

    public function testXlsx()
    {
        $type = new ExcelType();
        $localFile = new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../metadata/testfiles/flatdata-excel-xml.xlsx'), $type);
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
            'name' => 'flatdata-excel-xml.xlsx',
            'type' => $type,
            'size' => 8895,
            'count' => 2
        ), $info);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }
}
