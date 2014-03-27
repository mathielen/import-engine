<?php
namespace DataImportEngine\Mapping\Converter\Provider;

interface ConverterProviderInterface
{

    public function converters();

    public function converter($id);

}
