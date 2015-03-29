<?php
namespace Mathielen\ImportEngine\Storage\Format\Discovery;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;
use Mathielen\ImportEngine\Storage\Format\ExcelFormat;
use Mathielen\ImportEngine\Storage\Format\XmlFormat;
use Mathielen\ImportEngine\Storage\Format\CompressedFormat;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class FileExtensionDiscoverStrategy extends AbstractDiscoverStrategy
{

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface::getFormat()
     */
    public function getFormat($uri)
    {
        $ext = pathinfo($uri, PATHINFO_EXTENSION);

        $type = $this->discoverFormat($ext, $uri);

        return $type;
    }

    private function discoverFormat($ext, $uri)
    {
        if (array_key_exists($ext, $this->formatFactories)) {
            return $this->formatFactories[$ext]->factor($uri);
        }

        return self::fileExtensionToFormat($ext);
    }

    public static function fileExtensionToFormat($ext)
    {
        //defaults
        switch ($ext) {
            case 'zip':
                /*if ($subInformation) {
                    list($subMimeType, $subFile) = explode('@', $subInformation);

                    return new CompressedFormat($subFile, 'zip', $this->mimeTypeToFormat($subMimeType));
                } else {*/

                    return new CompressedFormat();
                //}
            case 'csv':
            case 'txt':
                return new CsvFormat();
            case 'xls':
            case 'xlsx':
                return new ExcelFormat();
            case 'xml':
                return new XmlFormat();
            default:
                throw new InvalidConfigurationException("Unknown file-extension: '$ext'.");
        }
    }

}
