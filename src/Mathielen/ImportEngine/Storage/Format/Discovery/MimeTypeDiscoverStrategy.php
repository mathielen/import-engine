<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\Discovery\Mime\MimeTypeDiscoverer;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\Storage\Format\Factory\FormatFactory;

class MimeTypeDiscoverStrategy implements FormatDiscoverStrategyInterface
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

    public function addMimeTypeFactory($mimeType, FormatFactory $factory)
    {
        $this->mimeTypeFactories[$mimeType] = $factory;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface::getFormat()
     */
    public function getFormat($uri)
    {
        $mimeType = $this->mimetypeDiscoverer->getMimeType($uri);
        @list($mimeType, $subMimeType) = explode(' ', $mimeType);

        $type = $this->mimeTypeToFormat($uri, $mimeType, $subMimeType);

        return $type;
    }

    private function mimeTypeToFormat($uri, $mimeType, $subMimeType = null)
    {
        if (array_key_exists($mimeType, $this->mimeTypeFactories)) {
            return $this->mimeTypeFactories[$mimeType]->factor($uri);
        }

        //defaults
        switch ($mimeType) {
            /*case 'application/zip':
                //TODO
                return new ZipType($this->mimeTypeToFileType("zip://", $subMimeType));*/
            case 'text/plain':
                return new CsvFormat();
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return new ExcelFormat();
            case 'application/xml':
                return new XmlFormat();
            default:
                throw new \LogicException("Unknown mime-type: $mimeType. No registered factoried nor any default for $uri");
        }

        return null;
    }

}
