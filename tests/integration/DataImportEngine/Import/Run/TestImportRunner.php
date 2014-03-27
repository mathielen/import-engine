<?php
namespace DataImportEngine\Import\Run;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use DataImportEngine\Storage\Provider\LocalFileStorageProvider;
use DataImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use DataImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;
use DataImportEngine\Storage\Type\Factory\CsvAutoDelimiterTypeFactory;
use DataImportEngine\Importer\Importer;
use DataImportEngine\Import\Import;
use DataImportEngine\Import\Event\ImportEvent;
use DataImportEngine\Storage\ArrayStorage;

class TestImportRunner extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(ImportEvent::COMPILE, array($this, 'onCompile'));
        $eventDispatcher->addListener(ImportEvent::START, array($this, 'onStart'));
        $eventDispatcher->addListener(ImportEvent::AFTER_READ, array($this, 'onAfterRead'));
        $eventDispatcher->addListener(ImportEvent::AFTER_CONVERSION, array($this, 'onAfterConversion'));
        $eventDispatcher->addListener(ImportEvent::AFTER_WRITE, array($this, 'onAfterWrite'));
        $eventDispatcher->addListener(ImportEvent::FINISH, array($this, 'onFinish'));

        $finder = Finder::create()
            ->in('/var/www/import-engine/tests/metadata/testfiles')
            ->name('*');
        $lfsp = new LocalFileStorageProvider($finder);
        $lfsp->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy(array(
                    'text/plain' => new CsvAutoDelimiterTypeFactory()
                ))));

        $array = array();
        $targetStorage = new ArrayStorage($array);

        $importer = Importer::build($targetStorage)
            ->addSourceStorageProvider('myLocalFiles', $lfsp);

        $import = Import::build($importer)
            ->setSourceStorageProviderId('myLocalFiles')
            ->setSourceStorageId('/var/www/import-engine/tests/metadata/testfiles/testmapping.csv');

        $import->mappings()
            ->add('Anrede', 'salutation', 'upperCase')
            ->add('name', 'first_name', 'lowerCase');

        $importRunner = new ImportRunner($eventDispatcher);

        /*$expectedResult = array(
            'StrasseHausnr' => 'Mümmelstr 12',
            'Plz' => 42349,
            'Vorname' => 'Hans',
            'Nachname' => 'Meiser',
            'salutation' => 'Herrn'
        );

        $this->assertEquals($expectedResult, $importRunner->preview($import)['to']);*/
    }

    public function onCompile(ImportEvent $event)
    {
        echo "compile\n";
        print_r($event->currentRow());
    }

    public function onStart(ImportEvent $event)
    {
        echo "start\n";
        print_r($event->currentRow());
    }

    public function onAfterRead(ImportEvent $event)
    {
        echo "after Read\n";
        print_r($event->currentRow());
    }

    public function onAfterConversion(ImportEvent $event)
    {
        echo "after Conversion\n";
        print_r($event->currentRow());
    }

    public function onAfterWrite(ImportEvent $event)
    {
        echo "after Write\n";
        print_r($event->currentRow());
    }

    public function onFinish(ImportEvent $event)
    {
        echo "finish\n";
        print_r($event->currentRow());
    }

}
