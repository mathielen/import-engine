<?php
namespace Mathielen\DataImport\ItemConverter;

use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\ValueObject\ImportRun;

class ContextMergeConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContextMergeConverter
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ContextMergeConverter();
    }

    /**
     * @dataProvider getConvertData
     */
    public function testConvert(ImportProcessEvent $event, array $inputData, array $expectedResult)
    {
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
                $this->createEvent(ImportRun::create()->setContext('string')),
                array('a'=>1),
                array('a'=>1)
            ),
            array(
                $this->createEvent(ImportRun::create()->setContext(array('b'=>2))),
                array('a'=>1),
                array('a'=>1, 'b'=>2)
            ),
        );
    }

    private function createEvent($importRun=null)
    {
        return new ImportProcessEvent(
            new Import(
                $this->getMock('Mathielen\ImportEngine\Importer\ImporterInterface'),
                $this->getMock('Mathielen\ImportEngine\Storage\StorageInterface'),
                $importRun
            )
        );
    }

}
