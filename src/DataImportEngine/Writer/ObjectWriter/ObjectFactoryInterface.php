<?php
namespace DataImportEngine\Writer\ObjectWriter;

interface ObjectFactoryInterface
{

    public function factor(array $item);

    public function getClassname();

}
