<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;

class MimeTypeDiscoverStrategyTest extends \PHPUnit_Framework_TestCase
{

    private $mimeTypeDiscovererMock;
    private $discoverStrategy;

    protected function setUp()
    {
        $this->mimeTypeDiscovererMock = $this->getMock('Mathielen\ImportEngine\Storage\Format\Discovery\Mime\MimeTypeDiscoverer');

        $this->discoverStrategy = new MimeTypeDiscoverStrategy(
            array(),
            $this->mimeTypeDiscovererMock
        );

    }

    /**
     * @dataProvider getDefaultMimeFormats
     */
    public function testDefaultMimeFormats($mimeType, $expectedFormat)
    {
        $this->mimeTypeDiscovererMock
            ->expects($this->once())
            ->method('discoverMimeType')
            ->will($this->returnValue($mimeType));

        $actualFormat = $this->discoverStrategy->getFormat('uri');

        $this->assertEquals($expectedFormat, $actualFormat);
    }

    public function getDefaultMimeFormats()
    {
        return array(
            array('application/zip text/csv@subfile.csv', new CompressedFormat('subfile.csv', 'zip', new CsvFormat())),
            array('application/zip', new CompressedFormat()),
            array('text/csv', new CsvFormat()),
            array('text/plain', new CsvFormat()),
            array('application/vnd.ms-excel', new ExcelFormat()),
            array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', new ExcelFormat()),
            array('application/xml', new XmlFormat()),
        );
    }

    /**
     * @expectedException Mathielen\ImportEngine\Exception\InvalidConfigurationException
     * @expectedExceptionText Unknown mime-type: ''. No registered factory nor any default for uri ''
     */
    public function testInvalidMimeType()
    {
        $this->discoverStrategy->getFormat('uri');
    }

    public function testMimeFormatFactories()
    {
        $formatFactory = $this->getMock('Mathielen\ImportEngine\Storage\Format\Factory\FormatFactoryInterface');
        $formatFactory
            ->expects($this->once())
            ->method('factor')
            ->with('uri')
            ->will($this->returnValue('myFormat'));
        $this->discoverStrategy->addMimeTypeFactory('my/mimetype', $formatFactory);

        $this->mimeTypeDiscovererMock
            ->expects($this->once())
            ->method('discoverMimeType')
            ->will($this->returnValue('my/mimetype'));

        $this->assertEquals('myFormat', $this->discoverStrategy->getFormat('uri'));
    }

}
