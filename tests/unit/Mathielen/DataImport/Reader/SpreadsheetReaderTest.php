<?php
namespace Mathielen\DataImport\Reader;

class SpreadsheetReaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //not ready yet
        $this->markTestSkipped();

        if (!extension_loaded('zip')) {
            $this->markTestSkipped();
        }
    }

    public function testGetFields()
    {
        $file = new \SplFileObject(__DIR__.'/../../../../metadata/testfiles/data_column_headers.xlsx');
        $reader = new SpreadsheetReader($file, 0);
        $this->assertEquals(array('id', 'number', 'description'), $reader->getFields());
        $this->assertEquals(array('id', 'number', 'description'), $reader->getColumnHeaders());
    }

    public function testCountWithoutHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../../metadata/testfiles/data_no_column_headers.xls');
        $reader = new SpreadsheetReader($file);
        $this->assertEquals(3, $reader->count());
    }

    public function testCountWithHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../../../../metadata/testfiles/data_column_headers.xlsx');
        $reader = new SpreadsheetReader($file, 0);
        $this->assertEquals(3, $reader->count());
    }

    public function testIterate()
    {
        $file = new \SplFileObject(__DIR__.'/../../../../metadata/testfiles/data_column_headers.xlsx');
        $reader = new SpreadsheetReader($file, 0);
        foreach ($reader as $row) {
            $this->assertInternalType('array', $row);
            $this->assertEquals(array('id', 'number', 'description'), array_keys($row));
        }
    }

    public function testMultiSheet()
    {
        $file = new \SplFileObject(__DIR__.'/../../../../metadata/testfiles/data_multi_sheet.xls');
        $sheet1reader = new SpreadsheetReader($file, null, 0);
        $this->assertEquals(3, $sheet1reader->count());

        $sheet2reader = new SpreadsheetReader($file, null, 1);
        $this->assertEquals(2, $sheet2reader->count());
    }
}
