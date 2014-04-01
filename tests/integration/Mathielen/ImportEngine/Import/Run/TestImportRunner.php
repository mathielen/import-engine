<?php
namespace Mathielen\ImportEngine\Import\Run;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\Storage\Type\Factory\CsvAutoDelimiterTypeFactory;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Event\ImportEvent;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Storage\Provider\FinderFileStorageProvider;

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
            ->in(__DIR__ . '/../../../../../metadata/testfiles')
            ->name('*');

        $lfsp = new FinderFileStorageProvider($finder);
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
            ->setSourceStorageId(__DIR__ . '/../../../../../metadata/testfiles/testmapping.csv');

        $import->mappings()
            ->add('Anrede', 'salutation', 'upperCase')
            ->add('Name', 'name', 'lowerCase');

        $importRunner = new ImportRunner($eventDispatcher);

        $expectedResult = array(
            'StrasseHausnr' => 'Mümmelstr 12',
            'Plz' => 42349,
            'name' => 'hans meiser',
            'salutation' => 'MR.'
        );

        $previewResult = $importRunner->preview($import, 0);
        $this->assertEquals($expectedResult, $previewResult['to']);
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
