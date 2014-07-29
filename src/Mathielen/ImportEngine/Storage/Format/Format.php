<?php
namespace Mathielen\ImportEngine\Storage\Format;

class Format
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

    public function toArray()
    {
        return get_object_vars($this);
    }

}
