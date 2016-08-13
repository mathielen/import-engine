<?php

namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

interface ConverterProviderInterface
{
    public function get($id);
    public function has($id);
}
