<?php
namespace Mathielen\DataImport\Reader;

class ServiceReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testRead()
    {
        $reader = new ServiceReader(array($this, 'read'));

        $this->assertEquals(1, count($reader));
        $this->assertEquals(array('field1'), $reader->getFields());
        $this->assertEquals(array('field1'=>'123'), $reader->current());
    }

    public function read()
    {
        return array(array('field1'=>'123'));
    }

}
