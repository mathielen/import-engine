<?php
namespace Mathielen\ImportEngine\Storage;

class ArrayStorageTest extends \PHPUnit_Framework_TestCase
{

    public function testInfo()
    {
        $a = [];
        $storage = new ArrayStorage($a);

        $this->assertEquals(new StorageInfo([
            'name' => 'Array Storage',
            'format' => 'Array Storage',
            'count' => 0
        ]), $storage->info());
    }

}
