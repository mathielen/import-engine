<?php
namespace Mathielen\ImportEngine\Storage;

class ServiceStorageTest extends \PHPUnit_Framework_TestCase
{

    public function testIsCalledService()
    {
        $storage = new ServiceStorage([$this, 'dummy']);

        $this->assertTrue($storage->isCalledService($this));
    }

    public function testInfo()
    {
        $storage = new ServiceStorage([$this, 'dummy']);

        $this->assertEquals(new StorageInfo([
            'name' => 'Mathielen\ImportEngine\Storage\ServiceStorageTest->dummy',
            'format' => 'Service method',
            'count' => 0
        ]), $storage->info());
    }

    public function dummy()
    {
        return [];
    }
}
