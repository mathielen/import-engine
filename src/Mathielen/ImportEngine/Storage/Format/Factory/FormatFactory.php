<?php
namespace Mathielen\ImportEngine\Storage\Format\Factory;

interface FormatFactory
{

    /**
     * @return \Mathielen\ImportEngine\Storage\Format\Format
     */
    public function factor($uri);

}
