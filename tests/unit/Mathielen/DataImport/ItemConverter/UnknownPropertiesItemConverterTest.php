<?php
namespace Mathielen\DataImport\ItemConverter;

class UnknownPropertiesItemConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getConvertData
     */
    public function testConvert(array $knownProperties, $targetProperty, $skipEmptyKey, $inputData, array $expectedResult)
    {
        $converter = new UnknownPropertiesItemConverter($knownProperties, $targetProperty, $skipEmptyKey);

        $this->assertEquals($expectedResult, $converter->convert($inputData));
    }

    public function getConvertData()
    {
        return array(
            array(
                array('a', 'b'),
                'target',
                true,
                array('a'=>1, 'b'=>2, 'c'=>3, ''=>''),
                array('a'=>1, 'b'=>2, 'target'=>array('c'=>3))
            ),
            array(
                array('a', 'b'),
                'target',
                false,
                array('a'=>1, 'b'=>2, ''=>''),
                array('a'=>1, 'b'=>2, 'target'=>array(''=>''))
            )
        );
    }

}
