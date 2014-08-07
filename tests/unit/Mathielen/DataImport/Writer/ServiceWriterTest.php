<?php
namespace Mathielen\DataImport\Writer;

use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;

class ServiceWriterTest extends \PHPUnit_Framework_TestCase
{

    private $written;

    public function testWriteItem()
    {
        $writer = new ServiceWriter(array($this, 'write'));

        $item = array('field1' => 'a', 'field2'=>'b');
        $writer->writeItem($item);

        $this->assertEquals(array($item), $this->written);
    }

    public function testWriteItemWithDefaultObjectFactory()
    {
        $writer = new ServiceWriter(array($this, 'write'), 'Mathielen\DataImport\Writer\MyClass');

        $item = array('field1' => 'a', 'field2'=>'b');
        $writer->writeItem($item);

        $expectedObject = new MyClass();
        $expectedObject->field1 = 'a';
        $expectedObject->field2 = 'b';

        $this->assertEquals(array($expectedObject), $this->written);
    }

    public function testWriteItemWithCustomObjectFactory()
    {
        $writer = new ServiceWriter(array($this, 'write'), new MyObjectfactory());

        $item = array('field1' => 'a', 'field2'=>'b');
        $writer->writeItem($item);

        $this->assertEquals(array(array('field1' => 'a', 'field2'=>'b', 'refactored'=>true)), $this->written);
    }

    public function write($item)
    {
        $this->written[] = $item;
    }

}

class MyClass
{

    public $field1;
    public $field2;

}

class MyObjectfactory implements ObjectFactoryInterface
{

    public function factor(array $item)
    {
        $item['refactored'] = true;

        return $item;
    }

}
