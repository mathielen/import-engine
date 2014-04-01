<?php
namespace Mathielen\DataImport\Writer\ObjectWriter;

/**
 * Converts array "rows" to objects via the serialization-hack
 * Wont work with nested objects or getter/setters etc. Use JmsSerializerObjectFactory for
 * more complex factoring.
 *
 * @see JmsSerializerObjectFactory
 */
class DefaultObjectFactory implements ObjectFactoryInterface
{

    private $classname;

    public function __construct($classname)
    {
        $this->classname = $classname;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    public function factor(array $item)
    {
        $stdClassObject = (object) $item;
        $object = unserialize(
            sprintf(
                'O:%d:"%s"%s',
                strlen($this->classname),
                $this->classname,
                substr(serialize($stdClassObject), 14)
            ));

        return $object;
    }
}
