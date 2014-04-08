<?php
namespace Mathielen\ImportEngine\Import\Run;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\Storage\Format\Factory\CsvAutoDelimiterFormatFactory;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Storage\Provider\FinderFileStorageProvider;
use Mathielen\DataImport\Event\ImportItemEvent;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;

class TestImportRunner extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(ImportItemEvent::AFTER_READ, array($this, 'onAfterRead'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_FILTER, array($this, 'onAfterFilter'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSION, array($this, 'onAfterConversion'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSIONFILTER, array($this, 'onAfterConversionFilter'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_WRITE, array($this, 'onAfterWrite'));

        $finder = Finder::create()
            ->in(__DIR__ . '/../../../../../metadata/testfiles')
            ->name('*');

        $lfsp = new FinderFileStorageProvider($finder);
        $lfsp->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy(array(
                    'text/plain' => new CsvAutoDelimiterFormatFactory()
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

        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $expectedResult = array(
            'StrasseHausnr' => 'Mümmelstr 12',
            'Plz' => 42349,
            'name' => 'hans meiser',
            'salutation' => 'MR.'
        );

        $previewResult = $importRunner->preview($import, 0);
        $this->assertEquals($expectedResult, $previewResult['to']);
    }

    public function onAfterRead(ImportItemEvent $event)
    {
        //echo "after Read\n";
    }

    public function onAfterFilter(ImportItemEvent $event)
    {
        //echo "after Filter\n";
    }

    public function onAfterConversion(ImportItemEvent $event)
    {
        //echo "after Conversion\n";
    }

    public function onAfterConversionFilter(ImportItemEvent $event)
    {
        //echo "after ConversionFilter\n";
    }

    public function onAfterWrite(ImportItemEvent $event)
    {
        //echo "after Write\n";
    }

}
