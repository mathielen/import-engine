<?php
namespace Mathielen\ImportEngine\Storage\Type\Discovery;

use Mathielen\ImportEngine\Storage\Type\CsvType;
use Mathielen\ImportEngine\Storage\Type\Discovery\Mime\MimeTypeDiscoverer;
use Mathielen\ImportEngine\Storage\Type\ExcelType;
use Mathielen\ImportEngine\Storage\Type\XmlType;
use Mathielen\ImportEngine\Storage\Type\ZipType;
use Mathielen\ImportEngine\Storage\Type\Factory\TypeFactory;

class MimeTypeDiscoverStrategy implements TypeDiscoverStrategyInterface
{

    /**
     * @var MimeTypeDiscoverer
     */
    private $mimetypeDiscoverer;

    private $mimeTypeFactories;

    public function __construct(array $mimeTypeFactories = array())
    {
        $this->mimeTypeFactories = $mimeTypeFactories;
        $this->mimetypeDiscoverer = new MimeTypeDiscoverer();
    }

    public function addMimeTypeFactory($mimeType, TypeFactory $factory)
    {
        $this->mimeTypeFactories[$mimeType] = $factory;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Type\Discovery\TypeDiscoverStrategyInterface::getType()
     */
    public function getType($uri)
    {
        $mimeType = $this->mimetypeDiscoverer->getMimeType($uri);
        @list($mimeType, $subMimeType) = explode(' ', $mimeType);

        $type = $this->mimeTypeToFileType($uri, $mimeType, $subMimeType);

        return $type;
    }

    private function mimeTypeToFileType($uri, $mimeType, $subMimeType = null)
    {
        if (array_key_exists($mimeType, $this->mimeTypeFactories)) {
            return $this->mimeTypeFactories[$mimeType]->factor($uri);
        }

        //defaults
        switch ($mimeType) {
            case 'application/zip':
                return new ZipType($this->mimeTypeToFileType($subMimeType));
            case 'text/plain':
                return new CsvType();
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return new ExcelType();
            case 'application/xml':
                return new XmlType();
        }

        return null;
    }

}
