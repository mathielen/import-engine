<?php
namespace Mathielen\ImportEngine\Validation;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Symfony\Component\Validator\Constraints\Email;
use Mathielen\DataImport\Filter\ValidatorFilter;
use Symfony\Component\Validator\Constraints\NotNull;

class ValidatorValidationTest extends \PHPUnit_Framework_TestCase
{

    private $validatorValidation;

    protected function setUp()
    {
        $this->validatorMock = $this->getMock('Symfony\Component\Validator\ValidatorInterface');

        $this->validatorValidation = ValidatorValidation::build($this->validatorMock);
    }

    public function testApply()
    {
        $this->validatorValidation->addSourceConstraint('sourceField', new Email());
        $this->validatorValidation->addTargetConstraint('targetField', new NotNull());

        $expectedSourceFilter = new ValidatorFilter($this->validatorMock);
        $expectedSourceFilter->setAllowExtraFields(true);
        $expectedSourceFilter->add('sourceField', new Email());

        $expectedTargetFilter = new ValidatorFilter($this->validatorMock);
        $expectedTargetFilter->setAllowExtraFields(true);
        $expectedTargetFilter->add('targetField', new NotNull());

        $workflow = $this->getMockBuilder('Ddeboer\DataImport\Workflow')->disableOriginalConstructor()->getMock();
        $workflow
            ->expects($this->once())
            ->method('addFilter')
            ->with($expectedSourceFilter);
        $workflow
            ->expects($this->once())
            ->method('addFilterAfterConversion')
            ->with($expectedTargetFilter);

        $this->validatorValidation->apply($workflow);
    }

    public function testGetViolations()
    {
        $sourceFilterMock = $this->getMockBuilder('Mathielen\DataImport\Filter\ValidatorFilter')->disableOriginalConstructor()->getMock();
        $sourceFilterMock
            ->expects($this->once())
            ->method('getViolations')
            ->will($this->returnValue('sourceViolation'));

        $targetFilterMock = $this->getMockBuilder('Mathielen\DataImport\Filter\ValidatorFilter')->disableOriginalConstructor()->getMock();
        $targetFilterMock
            ->expects($this->once())
            ->method('getViolations')
            ->will($this->returnValue('targetViolation'));

        $this->validatorValidation->setSourceValidatorFilter($sourceFilterMock);
        $this->validatorValidation->setTargetValidatorFilter($targetFilterMock);

        $workflow = $this->getMockBuilder('Ddeboer\DataImport\Workflow')->disableOriginalConstructor()->getMock();
        $this->assertEquals(
            array('source' => 'sourceViolation', 'target' => 'targetViolation'),
            $this->validatorValidation->getViolations()
        );
    }

}
