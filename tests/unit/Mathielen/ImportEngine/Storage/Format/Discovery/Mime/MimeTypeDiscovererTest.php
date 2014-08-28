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
        $is53 = version_compare(phpversion(), '5.3') >= 0 && version_compare(phpversion(), '5.4') < 0;

        return array(
            array('flatdata.csv', 'text/plain'),
            array('flatdata-excel-xml.xlsx', $is53?'application/vnd.ms-excel':'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            array('flatdata-excel.xls', 'application/vnd.ms-excel'),
            array('flatdata-excel-old.xls', 'application/vnd.ms-excel'),
            array('multiplefiles.zip', 'application/zip'),
            array('flatdata-csv-zip.zip', 'application/zip text/plain@testmapping.csv'),
        );
    }

}
