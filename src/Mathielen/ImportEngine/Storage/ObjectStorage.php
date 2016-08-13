<?php

namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Writer\ObjectWriter;
use Mathielen\ImportEngine\Storage\Parser\JmsMetadataParser;
use Mathielen\DataImport\Writer\ObjectWriter\ObjectFactoryInterface;
use Mathielen\DataImport\Writer\ObjectWriter\DefaultObjectFactory;

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
        } else {
            throw new \InvalidArgumentException('classOrObjectFactory must be of type string or ObjectFactoryInterface');
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
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Source\SourceInterface::reader()
     */
    public function reader()
    {
        //@TODO implement me
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Source\StorageInterface::writer()
     */
    public function writer()
    {
        $writer = new ObjectWriter($this->objectFactory);

        $storage = $this;
        $writer->setObjectHandler(function ($object) use ($storage) {
            $storage->attach($object);
        });

        return $writer;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Source\SourceInterface::info()
     */
    public function info()
    {
        return new StorageInfo(array(
        ));
    }
}
