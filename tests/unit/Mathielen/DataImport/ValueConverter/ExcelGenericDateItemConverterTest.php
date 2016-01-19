<?php
namespace Mathielen\DataImport\ValueConverter;

class ExcelGenericDateItemConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExcelGenericDateItemConverter
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ExcelGenericDateItemConverter();
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
                42338,
                '2015-11-30'
            )
        );
    }

}
