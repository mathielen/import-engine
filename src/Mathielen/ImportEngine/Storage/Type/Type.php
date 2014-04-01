<?php
namespace Mathielen\ImportEngine\Storage\Type;

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
