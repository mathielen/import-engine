<?php
namespace Mathielen\DataImport\Writer\ObjectWriter;

class DefaultObjectFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $of = new DefaultObjectFactory("Mathielen\DataImport\Writer\ObjectWriter\DummyClass");

        $item = array('name' => 'Peter');
        $actualObject = $of->factor($item);

        $expectedObject = new DummyClass('Peter');

        $this->assertEquals($expectedObject, $actualObject);
    }

}

class DummyClass
{

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

}
