<?php
namespace Mathielen\ImportEngine\Validation;

use Ddeboer\DataImport\Workflow;
use Mathielen\ImportEngine\Import\Filter\ValidatorFilter;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Ddeboer\DataImport\Filter\FilterInterface;

class Validation
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
            $this->sourceValidatorFilter = new ValidatorFilter($this->validator);
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
            $this->targetValidatorFilter = new ValidatorFilter($this->validator);
        }

        $this->targetValidatorFilter->add($field, $constraint);

        return $this;
    }

    public function setSourceValidatorFilter(FilterInterface $validatorFilter)
    {
        $this->sourceValidatorFilter = $validatorFilter;

        return $this;
    }

    public function setTargetValidatorFilter(FilterInterface $validatorFilter)
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
