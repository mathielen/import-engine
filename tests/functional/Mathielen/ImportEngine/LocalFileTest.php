<?php
namespace Mathielen\ImportEngine;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class LocalFileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getStorages
     * @medium
     */
    public function testImport($sourceFile, Format $format, Format $targetFormat=null)
    {
        if (!$targetFormat) {
            $targetFormat = $format;
        }

        $targetFile = tempnam('/tmp', 'test');
        @unlink($targetFile);

        $sourceStorage = new LocalFileStorage(new \SplFileInfo($sourceFile), $format);
        $targetStorage = new LocalFileStorage(new \SplFileInfo($targetFile), $targetFormat);

        $importer = Importer::build($targetStorage);

        $importConfiguration = new ImportConfiguration();
        $importRun = $importConfiguration->toRun();

        $import = Import::build($importer, $sourceStorage, $importRun);

        $importRunner = new ImportRunner();
        $importRunner->run($import);

        $this->assertFileExists($targetFile);
        //$this->assertFileEquals($sourceFile, $targetFile); excel files have binary differences :/
    }

    public function getStorages()
    {
        return array(
            array(__DIR__ . '/../../../metadata/testfiles/flatdata.csv', new CsvFormat(';', '"', '\\', false)),
            array(__DIR__ . '/../../../metadata/testfiles/flatdata-excel.xls', new ExcelFormat(false)),
            array(__DIR__ . '/../../../metadata/testfiles/flatdata-excel-xml.xlsx', new ExcelFormat(false)),
            array(__DIR__ . '/../../../metadata/testfiles/flatdata-csv-zip.zip', new CompressedFormat('testmapping.csv', 'zip', new CsvFormat()), new CsvFormat()),
            array(__DIR__ . '/../../../metadata/testfiles/hierarchicaldata-xml.xml', new XmlFormat()),
        );
    }

}
