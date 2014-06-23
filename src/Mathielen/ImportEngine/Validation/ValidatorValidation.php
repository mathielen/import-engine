<?php
namespace Mathielen\ImportEngine\Validation;

use Mathielen\DataImport\Workflow;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Mathielen\DataImport\Filter\ValidatorFilter;

class ValidatorValidation implements ValidationInterface
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ValidatorFilter
     */
    private $sourceValidatorFilter;

    /**
     * @var ValidatorFilter
     */
    private $targetValidatorFilter;

    /**
     * @return \Mathielen\ImportEngine\Validation\Validation
     */
    public static function build(ValidatorInterface $validator)
    {
        return new self($validator);
    }

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return \Mathielen\ImportEngine\Validation\Validation
     */
    public function addSourceConstraint($field, Constraint $constraint)
    {
        if (!$this->sourceValidatorFilter) {
            $this->setSourceValidatorFilter(new ValidatorFilter($this->validator));
            $this->sourceValidatorFilter->setAllowExtraFields(true); //@TODO configureble?
        }

        $this->sourceValidatorFilter->add($field, $constraint);

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Validation\Validation
     */
    public function addTargetConstraint($field, Constraint $constraint)
    {
        if (!$this->targetValidatorFilter) {
            $this->setTargetValidatorFilter(new ValidatorFilter($this->validator));
        }

        $this->targetValidatorFilter->add($field, $constraint);

        return $this;
    }

    public function setSourceValidatorFilter(ValidatorFilter $validatorFilter)
    {
        $this->sourceValidatorFilter = $validatorFilter;

        return $this;
    }

    public function setTargetValidatorFilter(ValidatorFilter $validatorFilter)
    {
        $this->targetValidatorFilter = $validatorFilter;

        return $this;
    }

    public function apply(Workflow $workflow)
    {
        if ($this->sourceValidatorFilter) {
            $workflow->addFilter($this->sourceValidatorFilter);
        }
        if ($this->targetValidatorFilter) {
            $workflow->addFilterAfterConversion($this->targetValidatorFilter);
        }
    }

    public function getViolations()
    {
        $violations = array('source'=>array(), 'target'=>array());
        if ($this->sourceValidatorFilter) {
            $violations['source'] = $this->sourceValidatorFilter->getViolations();
        }
        if ($this->targetValidatorFilter) {
            $violations['target'] = $this->targetValidatorFilter->getViolations();
        }

        return $violations;
    }

}
