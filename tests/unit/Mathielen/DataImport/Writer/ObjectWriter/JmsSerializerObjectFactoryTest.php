<?php
namespace Mathielen\DataImport\Writer\ObjectWriter;

class JmsSerializerObjectFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $serializerMock = $this->getMock('JMS\Serializer\SerializerInterface');

        $className = 'TestEntities\Dummy';
        $of = new JmsSerializerObjectFactory($className, $serializerMock);

        $expectedObject = new \TestEntities\Dummy('Peter');
        $expectedJson = '{"name":"Peter"}';

        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with($expectedJson, $className, 'json')
            ->will($this->returnValue($expectedObject));

        $item = array('NAME' => 'Peter');
        $actualObject = $of->factor($item);

        $this->assertEquals($expectedObject, $actualObject);
    }

}
