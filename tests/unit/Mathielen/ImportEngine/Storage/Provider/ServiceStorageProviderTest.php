<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\ServiceStorage;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class ServiceStorageProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceStorageProvider
     */
    private $serviceStorageProvider;

    protected function setUp()
    {
        $containerMock = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(new TestService()));

        $this->serviceStorageProvider = new ServiceStorageProvider($containerMock, array('serviceA'=>array('methods'=>array('myCallableMethod'))));
    }

    public function testStorageWithArguments()
    {
        $selection = new StorageSelection(array(new TestService(), 'myCallableMethod', 'arguments'=>array(1,2)));
        $actualResult = $this->serviceStorageProvider->storage($selection);

        $expectedResult = new ServiceStorage(array(new TestService(), 'myCallableMethod'), array(1,2));
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testStorage()
    {
        $callable = function () { return array('data'); };

        $selection = new StorageSelection($callable);
        $actualResult = $this->serviceStorageProvider->storage($selection);

        $expectedResult = new ServiceStorage($callable);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSelect()
    {
        $id = array(
            'service' => 'serviceA',
            'method' => 'myCallableMethod'
        );

        $actualResult = $this->serviceStorageProvider->select($id);
        $expectedResult = new StorageSelection(array(new TestService(), 'myCallableMethod', null));
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider getExceptionData
     * @expectedException InvalidArgumentException
     */
    public function testException($id)
    {
        $this->serviceStorageProvider->select($id);
    }

    public function getExceptionData()
    {
        return array(
            array('invalid_format'),
            array(array('service'=>'Unknown Service', 'method'=>'abc')),
            array(array('service'=>'serviceA', 'method'=>'Unknown Method'))
        );
    }

}

class TestService
{

    public function myCallableMethod() {}

}
