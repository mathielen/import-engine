<?php
namespace Mathielen\DataImport\Filter;

use Mathielen\DataImport\Event\ImportItemEvent;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints;

class ValidatorFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ValidatorFilter
     */
    private $validatorFilter;

    private $validatorMock;
    private $eventDispatcherMock;

    protected function setUp()
    {
        $this->validatorMock = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->eventDispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->validatorFilter = new ValidatorFilter(
            $this->validatorMock,
            array(),
            $this->eventDispatcherMock);
    }

    public function testOptions()
    {
        $this->validatorFilter->setAllowExtraFields(true);
        $this->validatorFilter->setAllowMissingFields(true);
        $this->validatorFilter->add('field1', new NotBlank());

        $item = array('field1', 'field2');
        $constraints = new Constraints\Collection(array(
            'allowExtraFields' => true,
            'allowMissingFields' => true,
            'fields' => array(
                'field1' => new NotBlank()
            )
        ));

        $this->validatorMock
            ->expects($this->once())
            ->method('validateValue')
            ->with($item, $constraints);

        $this->validatorFilter->filter($item);
    }

    /**
     * @dataProvider getSkipOnValidationData
     */
    public function testValidateFailed($skipOnValidation)
    {
        $this->validatorFilter->setSkipOnViolation($skipOnValidation);
        $item = array('field1', 'field2');
        $violations = array('error');

        $this->validatorMock
            ->expects($this->once())
            ->method('validateValue')
            ->with($item)
            ->will($this->returnValue($violations));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(ImportItemEvent::AFTER_VALIDATION, new ImportItemEvent(false));

        $filterResult = $this->validatorFilter->filter($item);

        $this->assertEquals(!$skipOnValidation, $filterResult);
        $this->assertEquals(array('1'=>$violations), $this->validatorFilter->getViolations());
    }

    public function getSkipOnValidationData()
    {
        return array(array(true), array(false));
    }

    public function testValidateSuccess()
    {
        $item = array('field1', 'field2');
        $violations = array();

        $this->validatorMock
            ->expects($this->once())
            ->method('validateValue')
            ->with($item)
            ->will($this->returnValue($violations));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(ImportItemEvent::AFTER_VALIDATION, new ImportItemEvent($item));

        $filterResult = $this->validatorFilter->filter($item);

        $this->assertTrue($filterResult);
        $this->assertEquals(array(), $this->validatorFilter->getViolations());
    }

    public function testPriority()
    {
        $this->assertEquals(64, $this->validatorFilter->getPriority());
    }

}
