<?php
namespace Mathielen\ImportEngine\Mapping\Converter\Provider;

interface ConverterProviderInterface
{

    public function getAll();

    public function get($id);

}
