<?php
namespace Mathielen\ImportEngine\Storage\Type\Factory;

interface TypeFactory
{

    /**
     * @return \Mathielen\ImportEngine\Storage\Type\Type
     */
    public function factor($uri);

}
