<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\JsonFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

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

        $actualFormat = $this->discoverStrategy->getFormat(new StorageSelection(null));

        $this->assertEquals($expectedFormat, $actualFormat);
    }

    public function getDefaultMimeFormats()
    {
        return array(
            array('application/zip text/csv@subfile.csv', new CompressedFormat('subfile.csv', 'zip', new CsvFormat())),
            array('application/zip', new CompressedFormat()),
            array('text/csv', new CsvFormat()),
            array('application/json', new JsonFormat()),
            array('text/plain', new CsvFormat()),
            array('application/vnd.ms-excel', new ExcelFormat()),
            array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', new ExcelFormat()),
            array('application/xml', new XmlFormat()),
        );
    }

    /**
     * @expectedException Mathielen\ImportEngine\Exception\InvalidConfigurationException
     */
    public function testInvalidMimeType()
    {
        $this->discoverStrategy->getFormat(new StorageSelection(null, 'uri'));
    }

    public function testMimeFormatFactories()
    {
        $formatFactory = $this->getMock('Mathielen\ImportEngine\Storage\Format\Factory\FormatFactoryInterface');
        $formatFactory
            ->expects($this->once())
            ->method('factor')
            ->with('uri')
            ->will($this->returnValue('myFormat'));
        $this->discoverStrategy->addFormatFactory('my/mimetype', $formatFactory);

        $this->mimeTypeDiscovererMock
            ->expects($this->once())
            ->method('discoverMimeType')
            ->will($this->returnValue('my/mimetype'));

        $this->assertEquals('myFormat', $this->discoverStrategy->getFormat(new StorageSelection(null, 'uri')));
    }

}
