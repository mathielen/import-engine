<?php
namespace Mathielen\DataImport\ItemConverter;

use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class ContextSupplyConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContextSupplyConverter
     */
    private $sut;

    /**
     * @dataProvider getConvertData
     */
    public function testConvert(ImportProcessEvent $event, array $inputData, array $expectedResult, $contextFieldname='context')
    {
        $this->sut = new ContextSupplyConverter($contextFieldname);

        $this->sut->onImportPrepare($event);

        $this->assertEquals($expectedResult, $this->sut->convert($inputData));
    }

    public function getConvertData()
    {
        return array(
            array(
                new ImportProcessEvent(),
                array('a'=>1),
                array('a'=>1)
            ),
            array(
                $this->createEvent(),
                array('a'=>1),
                array('a'=>1)
            ),
            array(
                $this->createEvent(ImportRun::create()->setContext(array('b'=>2))),
                array('a'=>1),
                array('a'=>1, 'mycontext'=>array('b'=>2)),
                'mycontext'
            ),
        );
    }

    private function createEvent($importRun=null)
    {
        return new ImportProcessEvent(
            new Import(
                $this->createMock('Mathielen\ImportEngine\Importer\ImporterInterface'),
                $this->createMock('Mathielen\ImportEngine\Storage\StorageInterface'),
                $importRun
            )
        );
    }

}
