<?php
namespace Mathielen\ImportEngine;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\ServiceStorage;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServiceTest extends \PHPUnit_Framework_TestCase
{

    private $dataWritten;

    /**
     * @medium
     */
    public function test()
    {
        $sourceStorage = new ServiceStorage(array($this, 'readData'));
        $targetStorage = new ServiceStorage(array($this, 'writeData'));

        $importer = Importer::build($targetStorage);

        $import = Import::build($importer, $sourceStorage);

        $eventDispatcher = new EventDispatcher();
        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $importRunner->run($import);

        $this->assertEquals(array(array('data1'), array('data2')), $this->dataWritten);
    }

    public function readData()
    {
        return array(array('data1'), array('data2'));
    }

    public function writeData($item)
    {
        $this->dataWritten[] = $item;
    }

}
