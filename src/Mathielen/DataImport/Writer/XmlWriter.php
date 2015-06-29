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

    /**
     * @var \DOMNode
     */
    private $rootNode;

    private $rowNodeName;
    private $rootNodeName;

    public function __construct(\SplFileObject $file, $rowNodeName='node', $rootNodeName = 'root')
    {
        $this->filename = $file->getRealPath();
        $this->rowNodeName = $rowNodeName;
        $this->rootNodeName = $rootNodeName;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        $this->xml = new \DOMDocument();
        $this->rootNode = $this->xml->createElement($this->rootNodeName);
        $this->xml->appendChild($this->rootNode);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Ddeboer\DataImport\Writer\WriterInterface::writeItem()
     */
    public function writeItem(array $item)
    {
        $newNode = $this->xml->createElement($this->rowNodeName);

        //attributes
        if (isset($item['@attributes']) && is_array($item['@attributes'])) {
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

        $this->rootNode->appendChild( $newNode );

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
