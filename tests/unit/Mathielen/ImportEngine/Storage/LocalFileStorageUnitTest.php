<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;

class LocalFileStorageUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAvailableFormats()
    {
        $storage = new LocalFileStorage (new \SplFileObject('tests/metadata/testfiles/100.csv'), new CsvFormat());

        $this->assertEquals([
            'csv',
            'excel',
            'xml',
            'json',
            'zlib'
        ], $storage->getAvailableFormats());
    }

}
