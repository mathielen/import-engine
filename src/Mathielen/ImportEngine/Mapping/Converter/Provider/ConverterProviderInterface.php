<?php
namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

interface ConverterProviderInterface
{

    public function converters();

    public function converter($id);

}
