<?php
namespace Mathielen\DataImport\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;

/**
 * Writes data to a xml file
 */
class XmlWriter implements WriterInterface
{

    /**
     * @var \DOMDocument
     */
    private $xml;
    private $filename;

    public function __construct(\SplFileObject $file)
    {
        $this->filename = $file->getRealPath();
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        $this->xml = new \DOMDocument();

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Ddeboer\DataImport\Writer\WriterInterface::writeItem()
     */
    public function writeItem(array $item)
    {
        $newNode = $this->xml->createElement("node");

        foreach ($item as $key=>$value) {
            $attr = $this->xml->createAttribute($key);
            $attr->value = $value;
            $newNode->appendChild($attr);
        }

        $this->xml->appendChild( $newNode );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function finish()
    {
        $this->xml->save($this->filename);

        return $this;
    }
}
