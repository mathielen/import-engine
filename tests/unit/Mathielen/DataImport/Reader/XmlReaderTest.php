<?php
namespace Mathielen\DataImport\Reader;

class XmlReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testSimple()
    {
        $reader = new XmlReader(new \SplFileObject('tests/metadata/testfiles/hierarchicaldata-xml.xml'));

        $this->assertEquals(2, count($reader));
        //$this->assertEquals(array('field1'), $reader->getFields());
        $this->assertEquals(array('value1'=>'1', '@attributes'=>array('attr1'=>'a')), $reader->current());

        $actualData = array();
        $expectedData = array(
            array('value1'=>'1', '@attributes'=>array('attr1'=>'a')),
            array('value1'=>'2', '@attributes'=>array('attr2'=>'b'))
        );
        foreach ($reader as $key=>$row) {
            $actualData[] = $row;
        }
        $this->assertEquals($expectedData, $actualData);
    }

    public function testXpath()
    {
        $reader = new XmlReader(new \SplFileObject('tests/metadata/testfiles/hierarchicaldata-xml.xml'), "/root/node[@attr2='b']");

        $this->assertEquals(1, count($reader));
        //$this->assertEquals(array('field1'), $reader->getFields());
        $this->assertEquals(array('value1'=>'2', '@attributes'=>array('attr2'=>'b')), $reader->current());

        $actualData = array();
        $expectedData = array(
            array('value1'=>'2', '@attributes'=>array('attr2'=>'b'))
        );
        foreach ($reader as $key=>$row) {
            $actualData[] = $row;
        }
        $this->assertEquals($expectedData, $actualData);
    }

}
