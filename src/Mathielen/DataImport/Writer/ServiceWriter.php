<?php
namespace Mathielen\DataImport\Writer;

/**
 * Writes data to a given service
 */
class ServiceWriter extends ObjectWriter
{

    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable, $classOrObjectFactory=null)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("Cannot call callable");
        }
        $this->callable = $callable;

        parent::__construct(new \SplObjectStorage(), $classOrObjectFactory);
    }

    protected function write($objectOrItem)
    {
        return call_user_func_array($this->callable, array($objectOrItem));
    }

}
