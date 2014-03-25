<?php
namespace DataImportEngine\Storage\Type\Discovery;

use DataImportEngine\Storage\Type\Type;
interface TypeDiscoverStrategyInterface
{

    /**
     * @return Type
     */
    public function getType($uri);

}
