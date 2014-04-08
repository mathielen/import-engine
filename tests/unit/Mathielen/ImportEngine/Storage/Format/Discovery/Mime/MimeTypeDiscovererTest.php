<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery\Mime;

class MimeTypeDiscovererTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getDiscoverMimeTypeData
     */
    public function testDiscoverMimeType($file, $expectedMimetype)
    {
        $mimeTypeDiscoverer = new MimeTypeDiscoverer();
        $actualMimeType = $mimeTypeDiscoverer->discoverMimeType(__DIR__ . '/../../../../../../../metadata/testfiles/'.$file);

        $this->assertEquals($expectedMimetype, $actualMimeType);
    }

    public function getDiscoverMimeTypeData()
    {
        return array(
            array('flatdata.csv', 'text/plain'),
            array('flatdata-excel-xml.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            array('multiplefiles.zip', 'application/zip'),
            array('testmapping.zip', 'application/zip text/plain@testmapping.csv'),
        );
    }

}
