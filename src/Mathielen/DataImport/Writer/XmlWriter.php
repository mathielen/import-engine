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

    private $nodeName;

    public function __construct(\SplFileObject $file, $nodeName='node')
    {
        $this->filename = $file->getRealPath();
        $this->nodeName = $nodeName;
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
        $newNode = $this->xml->createElement($this->nodeName);

        //attributes
        if (array_key_exists('@attributes', $item) && is_array($item['@attributes'])) {
            foreach ($item['@attributes'] as $key => $value) {
                $attr = $this->xml->createAttribute($key);
                $attr->value = $value;
                $newNode->appendChild($attr);
            }
            unset($item['@attributes']);
        }

        //values
        foreach ($item as $key => $value) {
            $node = $this->xml->createElement($key);
            $node->nodeValue = $value;
            $newNode->appendChild($node);
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
