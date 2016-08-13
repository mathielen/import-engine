<?php

namespace Mathielen\DataImport\Writer\ObjectWriter;

interface ObjectFactoryInterface
{
    public function factor(array $item);
}
