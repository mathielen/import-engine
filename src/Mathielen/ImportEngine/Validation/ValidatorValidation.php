<?php

namespace Mathielen\ImportEngine\Validation;

use Ddeboer\DataImport\Workflow;
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
     * @return ValidatorValidation
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
     * @return ValidatorValidation
     */
    public function addSourceConstraint($field, Constraint $constraint)
    {
        if (!$this->sourceValidatorFilter) {
            $this->setSourceValidatorFilter(new ValidatorFilter($this->validator));
            $this->sourceValidatorFilter->setAllowExtraFields(true);
        }

        $this->sourceValidatorFilter->add($field, $constraint);

        return $this;
    }

    /**
     * @return ValidatorValidation
     */
    public function addTargetConstraint($field, Constraint $constraint)
    {
        if (!$this->targetValidatorFilter) {
            $this->setTargetValidatorFilter(new ValidatorFilter($this->validator));
            $this->targetValidatorFilter->setAllowExtraFields(true);
        }

        $this->targetValidatorFilter->add($field, $constraint);

        return $this;
    }

    /**
     * @return ValidatorValidation
     */
    public function setSourceValidatorFilter(ValidatorFilter $validatorFilter)
    {
        $this->sourceValidatorFilter = $validatorFilter;

        return $this;
    }

    /**
     * @return ValidatorValidation
     */
    public function setTargetValidatorFilter(ValidatorFilter $validatorFilter)
    {
        $this->targetValidatorFilter = $validatorFilter;

        return $this;
    }

    /**
     * @return ValidatorValidation
     */
    public function apply(Workflow $workflow)
    {
        if ($this->sourceValidatorFilter) {
            $this->sourceValidatorFilter->reset();
            $workflow->addFilter($this->sourceValidatorFilter);
        }
        if ($this->targetValidatorFilter) {
            $this->targetValidatorFilter->reset();
            $workflow->addFilterAfterConversion($this->targetValidatorFilter);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        $violations = array('source' => array(), 'target' => array());
        if ($this->sourceValidatorFilter) {
            $violations['source'] = $this->sourceValidatorFilter->getViolations();
        }
        if ($this->targetValidatorFilter) {
            $violations['target'] = $this->targetValidatorFilter->getViolations();
        }

        return $violations;
    }
}
