<?php
namespace DataImportEngine\Storage;

use DataImportEngine\Source\SourceProvider;
use DataImportEngine\Source\SourceFactory;
class TestLocalFileStorage extends \PHPUnit_Framework_TestCase
{

    public function testCsv()
    {
        /*$sourceProvider = new SourceProvider(array(
            //'database' => new DoctrineSource('localhost', ''),
            'myLocalFiles' => array('local', __DIR__ . '/../../metadata/testfiles/'),
        ));

        $sources = $sourceProvider->getAvailableSources();
        $this->assertEquals(1, count($sources));

        $localFiles = $sourceProvider->getSource('myLocalFiles');*/

        $sourceFactory = new SourceFactory();
        $localFile = $sourceFactory->factor('local', __DIR__ . '/../../../metadata/testfiles/flatdata.csv');
        $localFile->setOptions(array('delimiter'=>'#'));
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
            'name' => 'flatdata.csv',
            'type' => 'CSV File (Separator = #)',
            'size' => 2847
        ), $info);

        $rows = $reader->count();
        $this->assertEquals(1, $rows);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(185, count($headers));
    }

    public function testXls()
    {
        $sourceFactory = new SourceFactory();
        $localFile = $sourceFactory->factor('local', __DIR__ . '/../../../metadata/testfiles/flatdata-excel.xls');
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
                'name' => 'flatdata-excel.xls',
                'type' => 'Excel File',
                'size' => 23552
        ), $info);

        $rows = $reader->count();
        $this->assertEquals(1, $rows);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }

    public function testXlsX()
    {
        $sourceFactory = new SourceFactory();
        $localFile = $sourceFactory->factor('local', __DIR__ . '/../../../metadata/testfiles/flatdata-excel-xml.xlsx');
        $reader = $localFile->reader();

        $info = $localFile->info();
        $this->assertEquals(array(
                'name' => 'flatdata-excel-xml.xlsx',
                'type' => 'Excel File',
                'size' => 8865
        ), $info);

        $rows = $reader->count();
        $this->assertEquals(1, $rows);

        $headers = $reader->getColumnHeaders();
        $this->assertEquals(2, count($headers));
    }

}
