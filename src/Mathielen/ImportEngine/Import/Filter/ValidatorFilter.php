<?php
namespace Mathielen\ImportEngine\Import\Filter;

use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Ddeboer\DataImport\Filter\ValidatorFilter as OriginalValidatorFilter;

class ValidatorFilter extends OriginalValidatorFilter
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    private $line = 1;

    private $options = array();

    private $violations = array();

    private $skipOnViolation = true;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function setSkipOnViolation($skipOnViolation)
    {
        $this->skipOnViolation = $skipOnViolation;

        return $this;
    }

    public function setAllowExtraFields($allowExtraFields)
    {
        $this->options['allowExtraFields'] = $allowExtraFields;
    }

    public function setAllowMissingFields($allowMissingFields)
    {
        $this->options['allowMissingFields'] = $allowMissingFields;
    }

    public function add($field, Constraint $constraint)
    {
        if (!isset($this->options['fields'][$field])) {
            $this->options['fields'][$field] = array();
        }

        $this->options['fields'][$field][] = $constraint;
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function filter(array $item)
    {
        $constraints = new Constraints\Collection($this->options);
        $list = $this->validator->validateValue($item, $constraints);

        if (count($list) > 0) {
            $this->violations[$this->line] = $list;
        }

        $this->line++;

        return !$this->skipOnViolation || 0 === count($list);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 64; //we should not be higher than the offset filter, which is 128
    }
}
