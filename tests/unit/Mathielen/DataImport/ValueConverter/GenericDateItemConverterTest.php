<?php
namespace Mathielen\DataImport\ValueConverter;

class GenericDateItemConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GenericDateItemConverter
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new GenericDateItemConverter();
    }

    /**
     * @dataProvider getConvertData
     */
    public function testConvert($inputData, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->sut->convert($inputData));
    }

    public function getConvertData()
    {
        return array(
            array(
                0,
                null
            ),
            array(
                '12.11.2015',
                '2015-11-12'
            ),
            array(
                '12.11.15',
                '2015-11-12'
            ),
            array(
                '121115',
                '2015-11-12'
            ),
            array(
                'abc',
                'abc'
            )
        );
    }

}
