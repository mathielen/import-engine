<?php
namespace DataImportEngine\Storage;

use DataImportEngine\Writer\ObjectWriter;
use DataImportEngine\Reader\ObjectReader;
use DataImportEngine\Storage\Parser\JmsMetadataParser;

class ObjectStorage extends \SplObjectStorage implements StorageInterface
{

    private $class;

    /**
     * @var JmsMetadataParser
     */
    private $metadataParser;

    public function __construct($class, JmsMetadataParser $metadataParser)
    {
        $this->class = $class;
        $this->metadataParser = $metadataParser;
    }

    public function getFields()
    {
        $fields = $this->metadataParser->parse(array('class' => $this->class, 'groups' => array()));

        return $fields;
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::reader()
     */
    public function reader()
    {
        //if ($this->list) {
            //return new ObjectReader($this->list);
        //} else {
            //$class = $this->class;
           // return new ObjectReader();
        //}
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {
        return new ObjectWriter($this->class);
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return array(
        );
    }

    /**
     * (non-PHPdoc)
     * @see \DataImportEngine\Source\SourceInterface::getType()
     */
    public function getType()
    {
    }

}
