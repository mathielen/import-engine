<?php
namespace Mathielen\DataImport\Writer\ObjectWriter;

class DefaultObjectFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $of = new DefaultObjectFactory('TestEntities\Dummy');

        $item = array('name' => 'Peter');
        $actualObject = $of->factor($item);

        $expectedObject = new \TestEntities\Dummy('Peter');

        $this->assertEquals($expectedObject, $actualObject);
    }

}
