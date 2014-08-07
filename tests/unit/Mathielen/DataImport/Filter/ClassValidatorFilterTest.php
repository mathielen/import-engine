<?php
namespace Mathielen\DataImport\Filter;

class ClassValidatorFilterTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $validatorMock = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $objectFactoryMock = $this->getMock('Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface');

        $classValidatorFilter = new ClassValidatorFilter(
            $validatorMock,
            $objectFactoryMock);

        $item = array('field1', 'field2');
        $object = new \stdClass();
        $violations = array('error');

        $objectFactoryMock
            ->expects($this->once())
            ->method('factor')
            ->with($item)
            ->will($this->returnValue($object));

        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($object)
            ->will($this->returnValue($violations));

        $filterResult = $classValidatorFilter->filter($item);

        $this->assertFalse($filterResult);
    }

}
