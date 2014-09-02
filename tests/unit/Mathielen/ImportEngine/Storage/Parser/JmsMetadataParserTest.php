<?php
namespace Mathielen\ImportEngine\Storage\Parser;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\MetadataFactory;

class JmsMetadataParserTest extends \PHPUnit_Framework_TestCase
{

    private $parser;

    private $metadataFactoryMock;
    private $propertyNamingStrategyMock;

    protected function setUp()
    {
        $this->metadataFactory = new MetadataFactory(
            new AnnotationDriver(
                new AnnotationReader()
            )
        );

        $this->parser = new JmsMetadataParser(
            $this->metadataFactory,
            new IdenticalPropertyNamingStrategy()
        );
       /* $this->storage
            ->expects($this->any())
            ->method('info')
            ->will($this->returnValue($info));
        $this->storage
            ->expects($this->any())
            ->method('getFields')
            ->will($this->returnValue(array('A', 'B')));*/
    }

    public function testParse()
    {
        $classname = 'Mathielen\ImportEngine\Storage\Parser\JmsMetadataParserTestClass';
        $result = $this->parser->parse(array('class'=>$classname, 'groups'=>array()));

        $this->assertEquals(4, count($result));
        $this->assertEquals('object (NestedType)', $result['property']['dataType']);
        $this->assertEquals('custom handler result for (IamACustomHandler)', $result['customHandler']['dataType']);
        $this->assertEquals('array of integers', $result['array']['dataType']);
        $this->assertEquals('array of objects (NestedType)', $result['assocArray']['dataType']);
    }

    public function testParseGroups()
    {
        $classname = 'Mathielen\ImportEngine\Storage\Parser\JmsMetadataParserTestClass';
        $result = $this->parser->parse(array('class'=>$classname, 'groups'=>array('group1')));

        $this->assertEquals(1, count($result));
    }

}

class JmsMetadataParserTestClass
{

    /**
     * @Type("Mathielen\ImportEngine\Storage\Parser\NestedType")
     */
    private $property;

    /**
     * @Type("IamACustomHandler")
     * @Groups({"group1"})
     */
    private $customHandler;

    /**
     * @Type("array<integer>")
     */
    private $array;

    /**
     * @Type("array<string, Mathielen\ImportEngine\Storage\Parser\NestedType>")
     */
    private $assocArray;

}

class NestedType
{

    /**
     * @Type("string")
     */
    private $property;

}
