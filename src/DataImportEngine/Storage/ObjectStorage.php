<?php
namespace DataImportEngine\Storage;

use DataImportEngine\Writer\ObjectWriter;
use DataImportEngine\Storage\Parser\JmsMetadataParser;
use DataImportEngine\Reader\IteratorReader;
use DataImportEngine\Writer\ValidationObjectWriter;
use DataImportEngine\Writer\ObjectWriter\ObjectFactoryInterface;
use DataImportEngine\Writer\ObjectWriter\DefaultObjectFactory;

class ObjectStorage extends \SplObjectStorage implements StorageInterface
{

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * @var JmsMetadataParser
     */
    private $metadataParser;

    public function __construct($classOrObjectFactory, JmsMetadataParser $metadataParser)
    {
        if (is_object($classOrObjectFactory) && $classOrObjectFactory instanceof ObjectFactoryInterface) {
            $objectFactory = $classOrObjectFactory;
        } elseif (is_string($classOrObjectFactory)) {
            $objectFactory = new DefaultObjectFactory($classOrObjectFactory);
        }

        $this->objectFactory = $objectFactory;
        $this->metadataParser = $metadataParser;
    }

    public function getFields()
    {
        $fields = $this->metadataParser->parse(array('class' => $this->objectFactory->getClassname(), 'groups' => array()));

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
        $writer = new ValidationObjectWriter($this->objectFactory);

        $storage = $this;
        $writer->setObjectHandler(function ($object) use ($storage) {
            $storage->attach($object);
        });

        return $writer;
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
