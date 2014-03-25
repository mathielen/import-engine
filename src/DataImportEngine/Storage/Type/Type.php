<?php
namespace DataImportEngine\Storage\Type;

class Type
{

    protected $id = null;
    protected $name = 'Unkown';

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }

}
