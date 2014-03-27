<?php
namespace DataImportEngine\Storage;

use DataImportEngine\Writer\ObjectWriter;
use DataImportEngine\Storage\Parser\JmsMetadataParser;
use DataImportEngine\Reader\IteratorReader;

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
        return new IteratorReader($this);
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
