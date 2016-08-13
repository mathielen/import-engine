<?php

namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\Discovery\Mime\MimeTypeDiscoverer;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\JsonFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class MimeTypeDiscoverStrategy extends AbstractDiscoverStrategy
{
    /**
     * @var MimeTypeDiscoverer
     */
    private $mimetypeDiscoverer;

    public function __construct(array $formatFactories = array(), $mimetypeDiscoverer = null)
    {
        if (is_null($mimetypeDiscoverer)) {
            $mimetypeDiscoverer = new MimeTypeDiscoverer();
        }

        $this->mimetypeDiscoverer = $mimetypeDiscoverer;
        parent::__construct($formatFactories);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface::getFormat()
     */
    public function getFormat(StorageSelection $selection)
    {
        $mimeType = $this->mimetypeDiscoverer->discoverMimeType($selection->getId());
        list($mimeType, $subInformation) = array_pad(explode(' ', $mimeType), 2, null);

        $type = $this->mimeTypeToFormat($mimeType, $selection->getId(), $subInformation);

        return $type;
    }

    private function mimeTypeToFormat($mimeType, $uri = null, $subInformation = null)
    {
        if (isset($this->formatFactories[$mimeType])) {
            return $this->formatFactories[$mimeType]->factor($uri);
        }

        //defaults
        switch ($mimeType) {
            case 'application/zip':
                if ($subInformation) {
                    list($subMimeType, $subFile) = explode('@', $subInformation);

                    return new CompressedFormat($subFile, 'zip', $this->mimeTypeToFormat($subMimeType));
                } else {
                    return new CompressedFormat();
                }
            case 'text/csv':
            case 'text/plain':
                return new CsvFormat();
            case 'application/json':
                return new JsonFormat();
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return new ExcelFormat();
            case 'application/xml':
                return new XmlFormat();
            default:
                throw new InvalidConfigurationException("Unknown mime-type: '$mimeType'. No registered factory nor any default for '$uri''");
        }
    }
}
