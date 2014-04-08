<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\ImportEngine\Storage\Format\Format;

interface StorageFormatInterface extends StorageInterface
{

    /**
     * @return Format
     */
    public function getFormat();

    /**
     * @return array
     */
    public function getAvailableFormats();

}
