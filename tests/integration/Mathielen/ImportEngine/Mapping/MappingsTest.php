<?php
namespace Mathielen\ImportEngine\Mapping;

use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\Workflow;

class MappingsTest extends \PHPUnit_Framework_TestCase
{

    public function test()
    {
        $mappings = new Mappings();
        $mappings
            ->add('foo', 'fooloo')
            ->add('baz', array('some' => 'else'));

        $expectedMappingItemConverter = new MappingItemConverter(array(
            'foo' => 'fooloo',
            'baz' =>array('some' => 'else')
        ));

        $workflow = $this->getMockBuilder('Ddeboer\DataImport\Workflow')->disableOriginalConstructor()->getMock();
        $workflow
            ->expects($this->once())
            ->method('addItemConverter')
            ->with($expectedMappingItemConverter);

        $mappings->apply($workflow, array());
    }

}
