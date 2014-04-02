<?php
namespace Mathielen\ImportEngine\Import\Filter;

use Ddeboer\DataImport\Filter\FilterInterface;
use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;
use Symfony\Component\Validator\ValidatorInterface;

class ClassValidatorFilter implements FilterInterface
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    private $line = 1;

    private $violations = array();

    private $skipOnViolation = true;

    public function __construct(
        ValidatorInterface $validator,
        ObjectFactoryInterface $objectFactory)
    {
        $this->validator = $validator;
        $this->objectFactory = $objectFactory;
    }

    public function setSkipOnViolation($skipOnViolation)
    {
        $this->skipOnViolation = $skipOnViolation;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Ddeboer\DataImport\Filter\FilterInterface::filter()
     */
    public function filter(array $item)
    {
        $object = $this->objectFactory->factor($item);
        $list = $this->validator->validate($object);

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
        return 256;
    }

    public function getViolations()
    {
        return $this->violations;
    }

}
