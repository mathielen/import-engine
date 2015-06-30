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
use Mathielen\ImportEngine\Storage\StorageInfo;
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
        $this->assertEquals(new StorageInfo(['name'=>'SELECT o FROM TestEntities\Address o', 'count'=>0, 'type'=>'DQL Query']), $targetStorage->info());

        $importer = Importer::build($targetStorage);

        $importConfiguration = new ImportConfiguration();
        $importRun = $importConfiguration->toRun();

        $import = Import::build($importer, $sourceStorage, $importRun);

        $eventDispatcher = new EventDispatcher();
        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $importRunner->run($import);

        $entities = self::$em
            ->getRepository('TestEntities\Address')
            ->findAll();

        //import worked
        $this->assertEquals(100, count($entities));

        $exportFile = '/tmp/doctrine_test.csv';
        @unlink($exportFile);
        $sourceStorage = new DoctrineStorage(self::$em, null, self::$em->createQuery("SELECT A FROM TestEntities\Address A WHERE A.zip LIKE '2%'"));
        $this->assertEquals(new StorageInfo(['name'=>"SELECT A FROM TestEntities\Address A WHERE A.zip LIKE '2%'", 'count'=>10, 'type'=>'DQL Query']), $sourceStorage->info());

        $targetStorage = new LocalFileStorage(new \SplFileInfo($exportFile), new CsvFormat());
        $importer = Importer::build($targetStorage);

        $importConfiguration = new ImportConfiguration();
        $importRun = $importConfiguration->toRun();

        $import = Import::build($importer, $sourceStorage, $importRun);

        $eventDispatcher = new EventDispatcher();
        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $importRunner->run($import);

        $this->assertFileExists($exportFile);
        $this->assertEquals(11, count(file($exportFile))); //+header
        $this->assertEquals(10, $import->getRun()->toArray()['statistics']['processed']);
    }

}
