<?php
namespace DataImportEngine\Workflow;

use DataImportEngine\Source\SourceFactory;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Filter\OffsetFilter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use DataImportEngine\Mapping\Mapping;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
class Test extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $sourceFactory = new SourceFactory();
        $localFile = $sourceFactory->factor('local', __DIR__ . '/../../../metadata/testfiles/testmapping.csv');
        $reader = $localFile->reader();

        $filter = new OffsetFilter(0, 2); //sneak

        $workflow = new Workflow($reader);
        $workflow
            ->addFilter($filter)
            ->addWriter(new CallbackWriter(function ($row) {
                print_r($row);
            }));

        $mapping = new Mapping();
        $mapping->addMapping('Anrede', 'Salutation', new CallbackValueConverter(function ($item) {
            switch ($item) {
                case 'Mr.':
                    return 'Herrn';
                case 'Mrs.':
                    return 'Frau';
            }
        }));
        $mapping->addMapping('Name', '', new CallbackItemConverter(function ($item) {
            list($vorname, $nachname) = explode(' ', $item['Name']);

            unset($item['Name']);
            $item['Vorname'] = $vorname;
            $item['Nachname'] = $nachname;

            return $item;
        }));
        $mapping->apply($workflow);

        $workflow->process();
    }

}
