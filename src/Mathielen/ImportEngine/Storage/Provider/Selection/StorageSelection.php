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

}
