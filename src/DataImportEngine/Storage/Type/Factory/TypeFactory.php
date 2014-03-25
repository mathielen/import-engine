<?php
namespace DataImportEngine\Storage\Type\Factory;

interface TypeFactory
{

    /**
     * @return \DataImportEngine\Storage\Type\Type
     */
    public function factor($uri);

}
