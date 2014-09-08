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

    protected $collectionConstraintOptions = array();

    protected $violations = array();

    /* if we skip everything else in the workflow when an error occurs, the "line" property of
     * the following filters arent correct. so you can disable the skip behavior here.
     */
    protected $skipOnViolation = true;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        ValidatorInterface $validator,
        $collectionConstraintOptions = array(),
        EventDispatcherInterface $eventDispatcher=null)
    {
        $this->validator = $validator;
        $this->collectionConstraintOptions = $collectionConstraintOptions;

        if ($eventDispatcher) {
            $this->setEventDispatcher($eventDispatcher);
        }
    }

    /**
     * @return ValidatorFilter
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @return ValidatorFilter
     */
    public function setSkipOnViolation($skipOnViolation)
    {
        $this->skipOnViolation = $skipOnViolation;

        return $this;
    }

    /**
     * @return ValidatorFilter
     */
    public function setAllowExtraFields($allowExtraFields)
    {
        $this->collectionConstraintOptions['allowExtraFields'] = $allowExtraFields;

        return $this;
    }

    /**
     * @return ValidatorFilter
     */
    public function setAllowMissingFields($allowMissingFields)
    {
        $this->collectionConstraintOptions['allowMissingFields'] = $allowMissingFields;

        return $this;
    }

    /**
     * @return ValidatorFilter
     */
    public function add($field, Constraint $constraint)
    {
        if (!isset($this->collectionConstraintOptions['fields'][$field])) {
            $this->collectionConstraintOptions['fields'][$field] = array();
        }

        $this->collectionConstraintOptions['fields'][$field][] = $constraint;

        return $this;
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

        $validationResult = 0 === count($list);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(ImportItemEvent::AFTER_VALIDATION, new ImportItemEvent($validationResult?$item:false));
        }

        return !$this->skipOnViolation || $validationResult;
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    protected function validate(array $item)
    {
        $constraints = new Constraints\Collection($this->collectionConstraintOptions);
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
