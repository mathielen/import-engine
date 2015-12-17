<?php
namespace Mathielen\ImportEngine\Import\Run;

use Mathielen\ImportEngine\Import\ImportBuilder;
use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\Storage\StorageLocator;
use Mathielen\ImportEngine\ValueObject\ImportRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\FormatDiscoverLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\Storage\Format\Factory\CsvAutoDelimiterFormatFactory;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Storage\Provider\FinderFileStorageProvider;
use Mathielen\DataImport\Event\ImportItemEvent;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;

class ImportRunnerTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(ImportItemEvent::AFTER_READ, array($this, 'onAfterRead'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_FILTER, array($this, 'onAfterFilter'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSION, array($this, 'onAfterConversion'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSIONFILTER, array($this, 'onAfterConversionFilter'));
        $eventDispatcher->addListener(ImportItemEvent::AFTER_WRITE, array($this, 'onAfterWrite'));

        $fileDir = __DIR__ . '/../../../../../metadata/testfiles';
        $finder = Finder::create()
            ->in($fileDir)
            ->name('*');

        $lfsp = new FinderFileStorageProvider($finder);
        $lfsp->setStorageFactory(
            new FormatDiscoverLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy(array(
                    'text/plain' => new CsvAutoDelimiterFormatFactory()
                ))));
        $storageLocator = new StorageLocator();
        $storageLocator->register('defaultProvider', $lfsp);

        $array = array();
        $targetStorage = new ArrayStorage($array);

        $importer = Importer::build($targetStorage);
        $importRepository = new ImporterRepository();
        $importRepository->register('defaultImporter', $importer);

        $importRequest = new ImportRequest($fileDir . '/100.csv', 'defaultProvider', 'defaultImporter');

        $importBuilder = new ImportBuilder(
            $importRepository,
            $storageLocator
        );
        $import = $importBuilder->buildFromRequest($importRequest);

        $import->mappings()
            ->add('prefix', 'Anrede', 'upperCase')
            ->add('name', 'Name', 'lowerCase');

        $importRunner = new ImportRunner(new DefaultWorkflowFactory($eventDispatcher));

        $expectedResult = array(
            'Name' => 'jennie abernathy',
            'Anrede' => 'MS.',
            'street' => '866 Hyatt Isle Apt. 888',
            'zip' => '65982',
            'city' => 'East Laurie',
            'phone' => '(551)436-0391',
            'email' => 'runolfsson.moriah@yahoo.com'
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
