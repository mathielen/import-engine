<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\Factory\FormatFactoryInterface;

abstract class AbstractDiscoverStrategy implements FormatDiscoverStrategyInterface
{

    /**
     * @var FormatFactoryInterface[]
     */
    protected $formatFactories;

    public function __construct(array $formatFactories = array())
    {
        $this->formatFactories = $formatFactories;
    }

    public function addFormatFactory($id, FormatFactoryInterface $factory)
    {
        $this->formatFactories[$id] = $factory;
    }

}
