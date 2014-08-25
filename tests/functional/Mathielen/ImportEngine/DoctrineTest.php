<?php
namespace Mathielen\ImportEngine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\DoctrineStorage;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\ValueObject\ImportRun;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DoctrineTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EntityManagerInterface
     */
    protected static $em = null;

    public static function setUpBeforeClass()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../../../metadata/TestEntities"), $isDevMode, null, null, false);

        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);

        // obtaining the entity manager
        self::$em =  EntityManager::create($connectionOptions, $config);

        $schemaTool = new SchemaTool(self::$em);

        $cmf = self::$em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    public static function tearDownAfterClass()
    {
        self::$em = NULL;
    }

    /**
     * @medium
     */
    public function testImportExport()
    {
        $sourceStorage = new LocalFileStorage(new \SplFileInfo(__DIR__ . '/../../../metadata/testfiles/100.csv'), new CsvFormat());
        $targetStorage = new DoctrineStorage(self::$em, 'TestEntities\Address');

        $importer = Importer::build($targetStorage);
        $importer->setSourceStorage($sourceStorage);

        $import = Import::build($importer);

        $importConfiguration = new ImportConfiguration();
        $importConfiguration->applyImport($import);
        $importRun = $importConfiguration->toRun();

        $eventDispatcher = new EventDispatcher();
        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $importRunner->run($importRun);

        $entities = self::$em
            ->getRepository('TestEntities\Address')
            ->findAll();

        //import worked
        $this->assertEquals(100, count($entities));

        $exportFile = '/tmp/doctrine_test.csv';
        $sourceStorage = new DoctrineStorage(self::$em, 'TestEntities\Address');
        $targetStorage = new LocalFileStorage(new  \SplFileInfo($exportFile), new CsvFormat());

        $importer = Importer::build($targetStorage);
        $importer->setSourceStorage($sourceStorage);

        $import = Import::build($importer);

        $importConfiguration = new ImportConfiguration();
        $importConfiguration->applyImport($import);
        $importRun = $importConfiguration->toRun();

        $eventDispatcher = new EventDispatcher();
        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $importRunner->run($importRun);

        $this->assertFileExists($exportFile);
    }

}
