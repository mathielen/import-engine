<?php
namespace Mathielen\DataImport\Event;

class ImportProcessEventTest extends \PHPUnit_Framework_TestCase
{

    public function testContextDelegation()
    {
        $context = 'myContext';

        $processEvent = new ImportProcessEvent();
        $processEvent->setContext($context);

        $newItemEvent = $processEvent->newItemEvent(array('myitem'));

        $this->assertEquals($context, $newItemEvent->getContext());
        $this->assertEquals($context, $processEvent->getContext());
    }

}
