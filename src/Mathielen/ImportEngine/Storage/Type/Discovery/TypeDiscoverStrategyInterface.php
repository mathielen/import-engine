<?php
namespace Mathielen\ImportEngine\Storage\Type\Discovery;

use Mathielen\ImportEngine\Storage\Type\Type;
interface TypeDiscoverStrategyInterface
{

    /**
     * @return Type
     */
    public function getType($uri);

}
