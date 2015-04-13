<?php
namespace Mathielen\ImportEngine;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class ArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @medium
     */
    public function test()
    {
        $data = array(
            array(
                'foo' => 'bar',
                'baz' => array(
                    'some' => 'value'
                )
            )
        );
        $targetData = array();

        $sourceStorage = new ArrayStorage($data);
        $targetStorage = new ArrayStorage($targetData);

        $importer = Importer::build($targetStorage);

        $importConfiguration = new ImportConfiguration();
        $importRun = $importConfiguration->toRun();

        $import = Import::build($importer, $sourceStorage, $importRun);
        $import
            ->mappings()
            ->add('foo', 'fooloo')
            ->add('baz', array('some' => 'else'));

        ImportRunner::build()
            ->run($import);

        $expectedData = array(
            array(
                'fooloo' => 'bar',
                'baz'    => array(
                    'else' => 'value'
                )
            )
        );

        $this->assertEquals($expectedData, $targetData);
    }

}
