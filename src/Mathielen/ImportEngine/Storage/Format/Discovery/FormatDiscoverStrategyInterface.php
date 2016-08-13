<?php

namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

interface FormatDiscoverStrategyInterface
{
    /**
     * @return Format
     */
    public function getFormat(StorageSelection $selection);
}
