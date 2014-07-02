<?php
namespace Mathielen\ImportEngine\ValueObject;

class StorageSelection
{

    protected $providerId = 'default';
    protected $id;
    protected $name;
    protected $impl;

    public function __construct($impl, $id=null, $name=null, $providerId = 'default')
    {
        $this->impl = $impl;
        $this->id = $id;
        $this->name = $name;
        $this->providerId = $providerId;
    }

    public function getProviderId()
    {
        return $this->providerId;
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
        //TODO some sort of serializable wrapper?
        if (is_array($this->impl)) {
            $reflectionClass = new \ReflectionClass($this->impl['class']);
            $this->impl = $reflectionClass->newInstanceArgs($this->impl['args']);
        }
    }

}
