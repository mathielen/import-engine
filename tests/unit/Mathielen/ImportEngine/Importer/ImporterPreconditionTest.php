<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;

class ImporterPreconditionTest extends \PHPUnit_Framework_TestCase
{

    private $storage;

    protected function setUp()
    {
        $info = array(
            'name' => 'file.csv',
            'format' => new CsvFormat()
        );

        $this->storage = $this->getMock('Mathielen\ImportEngine\Storage\StorageFormatInterface');
        $this->storage
            ->expects($this->any())
            ->method('info')
            ->will($this->returnValue($info));
        $this->storage
            ->expects($this->any())
            ->method('getFields')
            ->will($this->returnValue(array('A', 'B')));
    }

    /**
     * @dataProvider getFilenameData
     */
    public function testFilename($filename, $expectedResult)
    {
        $preCondition = new ImporterPrecondition();
        $this->assertEquals($expectedResult, $preCondition
            ->filename($filename)
            ->isSatisfiedBy($this->storage));
    }

    public function getFilenameData()
    {
        return array(
            array('file.csv', true),
            array('^.*\.csv$', true),
            array('f1le.csv', false)
        );
    }

    /**
     * @dataProvider getFormatData
     */
    public function testFormat($format, $expectedResult)
    {
        $preCondition = new ImporterPrecondition();
        $this->assertEquals($expectedResult, $preCondition
            ->format($format)
            ->isSatisfiedBy($this->storage));
    }

    public function getFormatData()
    {
        return array(
            array('csv', true),
            array('excel', false)
        );
    }

    /**
     * @expectedException \Mathielen\ImportEngine\Exception\InvalidConfigurationException
     */
    public function testFormatException()
    {
        $storage = $this->getMock('Mathielen\ImportEngine\Storage\StorageInterface');

        $preCondition = new ImporterPrecondition();
        $preCondition
            ->format('csv')
            ->isSatisfiedBy($storage);
    }

    public function testFieldcount()
    {
        $preCondition = new ImporterPrecondition();
        $this->assertTrue($preCondition
            ->fieldcount(2)
            ->isSatisfiedBy($this->storage));

        $preCondition = new ImporterPrecondition();
        $this->assertFalse($preCondition
            ->fieldcount(1)
            ->isSatisfiedBy($this->storage));
    }

    /**
     * @dataProvider getAnyFieldData
     */
    public function testAnyField($field, $expectedResult)
    {
        $preCondition = new ImporterPrecondition();
        $this->assertEquals($expectedResult, $preCondition
            ->field($field)
            ->isSatisfiedBy($this->storage));
    }

    public function getAnyFieldData()
    {
        return array(
            array('a', true),
            array('z', false)
        );
    }

    /**
     * @dataProvider getFieldsetData
     */
    public function testFieldset($fieldset, $expectedResult)
    {
        $preCondition = new ImporterPrecondition();
        $this->assertEquals($expectedResult, $preCondition
            ->fieldset($fieldset)
            ->isSatisfiedBy($this->storage));
    }

    public function getFieldsetData()
    {
        return array(
            array(array('a', 'b'), true),
            array(array('b', 'a'), false)
        );
    }

}
