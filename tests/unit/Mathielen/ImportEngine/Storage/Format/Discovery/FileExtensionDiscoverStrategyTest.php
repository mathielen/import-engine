<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;

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
        $actualFormat = $this->discoverStrategy->getFormat('file.'.$ext);

        $this->assertEquals($expectedFormat, $actualFormat);
    }

    public function getDefaultExtensions()
    {
        return array(
            //array('application/zip text/csv@subfile.csv', new CompressedFormat('subfile.csv', 'zip', new CsvFormat())),
            array('zip', new CompressedFormat()),
            array('csv', new CsvFormat()),
            array('txt', new CsvFormat()),
            array('xls', new ExcelFormat()),
            array('xlsx', new ExcelFormat()),
            array('xml', new XmlFormat()),
        );
    }

    /**
     * @expectedException Mathielen\ImportEngine\Exception\InvalidConfigurationException
     */
    public function testInvalidExt()
    {
        $this->discoverStrategy->getFormat('uri');
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

        $this->assertEquals('myFormat', $this->discoverStrategy->getFormat('uri/uri.ext'));
    }

}
