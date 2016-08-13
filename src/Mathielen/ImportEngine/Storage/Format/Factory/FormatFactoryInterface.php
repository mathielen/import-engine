<?php

namespace Mathielen\ImportEngine\Storage\Format\Factory;

interface FormatFactoryInterface
{
    /**
     * @return \Mathielen\ImportEngine\Storage\Format\Format
     */
    public function factor($uri);
}
