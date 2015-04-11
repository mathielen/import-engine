<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class FileExtensionDiscoverStrategyTest extends \PHPUnit_Framework_TestCase
{

    private $discoverStrategy;

    protected function setUp()
    {
        $this->discoverStrategy = new FileExtensionDiscoverStrategy(
            array()
        );
    }

    /**
     * @dataProvider getDefaultExtensions
     */
    public function testDefaultExtensions($ext, $expectedFormat)
    {
        $actualFormat = $this->discoverStrategy->getFormat($ext);

        $this->assertEquals($expectedFormat, $actualFormat);
    }

    public function getDefaultExtensions()
    {
        return array(
            //array('application/zip text/csv@subfile.csv', new CompressedFormat('subfile.csv', 'zip', new CsvFormat())),
            array(new StorageSelection(null, 'file.zip'), new CompressedFormat()),
            array(new StorageSelection(null, 'file.csv'), new CsvFormat()),
            array(new StorageSelection(null, 'file.txt'), new CsvFormat()),
            array(new StorageSelection(null, 'file.xls'), new ExcelFormat()),
            array(new StorageSelection(null, 'file.xlsx'), new ExcelFormat()),
            array(new StorageSelection(null, 'file.xml'), new XmlFormat()),
        );
    }

    /**
     * @expectedException \Mathielen\ImportEngine\Exception\InvalidConfigurationException
     */
    public function testInvalidExt()
    {
        $this->discoverStrategy->getFormat(new StorageSelection(null, 'uri'));
    }

    public function testExtFormatFactories()
    {
        $formatFactory = $this->getMock('Mathielen\ImportEngine\Storage\Format\Factory\FormatFactoryInterface');
        $formatFactory
            ->expects($this->once())
            ->method('factor')
            ->with('uri/uri.ext')
            ->will($this->returnValue('myFormat'));
        $this->discoverStrategy->addFormatFactory('ext', $formatFactory);

        $this->assertEquals('myFormat', $this->discoverStrategy->getFormat(new StorageSelection(null, 'uri/uri.ext')));
    }

}
