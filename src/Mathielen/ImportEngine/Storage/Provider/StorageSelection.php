<?php
namespace Mathielen\ImportEngine\Storage\Provider;

class StorageSelection
{

    private $id;
    private $name;
    private $impl;

    public function __construct($id, $name, $impl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->impl = $impl;
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
