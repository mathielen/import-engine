<?php

namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareConverterProvider extends DefaultConverterProvider implements ConverterProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    public function get($id)
    {
        $converter = parent::get($id);
        if (!$converter) {
            $converter = $this->container->get($id);
        }

        return $converter;
    }

    public function has($id)
    {
        return parent::has($id) || $this->container->has($id);
    }
}
