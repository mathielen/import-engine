<?php
namespace Mathielen\DataImport\ItemConverter;

use TestEntities\Address;

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

    public function testFromClass()
    {
        $converter = UnknownPropertiesItemConverter::fromClass(Address::class);

        $this->assertEquals(['name' => 1, 'ATTRIBUTES' => ['unknown' => 1]], $converter->convert(['name' => 1, 'unknown' => 1]));
    }

    public function getConvertData()
    {
        return array(
            array(
                array('a', 'b', 'property_with_underscore'),
                'target',
                true,
                array('a' => 1, 'b' => 2, 'c' => 3, '' => '', 'propertywithunderscore' => 1),
                array('a' => 1, 'b' => 2, 'target' => array('c' => 3), 'propertywithunderscore' => 1)
            ),
            array(
                array('a', 'b'),
                'target',
                false,
                array('a' => 1, 'b' => 2, '' => ''),
                array('a' => 1, 'b' => 2, 'target' => array('' => ''))
            )
        );
    }

}
