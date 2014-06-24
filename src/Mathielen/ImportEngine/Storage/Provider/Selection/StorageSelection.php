<?php
namespace Mathielen\ImportEngine\Storage\Provider\Selection;

class StorageSelection
{

    private $id;
    private $name;
    private $impl;

    public function __construct($impl, $id=null, $name=null)
    {
        $this->impl = $impl;
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImpl()
    {
        return $this->impl;
    }

    public function prePersist()
    {
        if ($this->impl instanceof \SplFileObject) {

            //TODO some sort of serializable wrapper?
            $this->impl = array(
                'class'=>get_class($this->impl),
                'args'=>array($this->impl->getRealPath())
            );
        }
    }

    public function postLoad()
    {
        if (is_array($this->impl)) {
            $reflectionClass = new \ReflectionClass($this->impl['class']);
            $this->impl = $reflectionClass->newInstanceArgs($this->impl['args']);
        }
    }

}
