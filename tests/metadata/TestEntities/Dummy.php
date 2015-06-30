<?php
namespace TestEntities;

class Dummy
{

    private $name;

    public function __construct($name=null)
    {
        $this->name = $name;
    }

    public function onNewData()
    {}

}
