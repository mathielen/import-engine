<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\Format;

interface FormatDiscoverStrategyInterface
{

    /**
     * @return Format
     */
    public function getFormat($uri);

}
