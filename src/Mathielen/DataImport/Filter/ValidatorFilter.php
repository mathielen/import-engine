<?php
namespace Mathielen\DataImport\Filter;

use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Ddeboer\DataImport\Filter\ValidatorFilter as OriginalValidatorFilter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mathielen\DataImport\Event\ImportItemEvent;

/**
 * Validationfilter with more options and an eventDispatcher.
 */
class ValidatorFilter extends OriginalValidatorFilter
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected $line = 1;

    protected $options = array();

    protected $violations = array();

    protected $skipOnViolation = true;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher=null)
    {
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        $list = $this->validate($item);

        if (count($list) > 0) {
            $this->violations[$this->line] = $list;
        }

        $this->line++;

        $validationResult = !$this->skipOnViolation || 0 === count($list);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_VALIDATION, new ImportItemEvent($validationResult));
        }

        return $validationResult;
    }

    protected function validate(array $item)
    {
        $constraints = new Constraints\Collection($this->options);
        $list = $this->validator->validateValue($item, $constraints);

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 64; //we should not be higher than the offset filter, which is 128
    }
}
