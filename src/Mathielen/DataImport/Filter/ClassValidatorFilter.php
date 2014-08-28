<?php
namespace Mathielen\DataImport\Filter;

use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClassValidatorFilter extends ValidatorFilter
{

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    public function __construct(
        ValidatorInterface $validator,
        ObjectFactoryInterface $objectFactory,
        EventDispatcherInterface $eventDispatcher=null)
    {
        parent::__construct($validator, array(), $eventDispatcher);
        $this->objectFactory = $objectFactory;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\DataImport\Filter\ValidatorFilter::validate()
     */
    protected function validate(array $item)
    {
        $object = $this->objectFactory->factor($item);
        $list = $this->validator->validate($object);

        return $list;
    }

}
