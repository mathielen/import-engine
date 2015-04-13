<?php
namespace Mathielen\ImportEngine\ValueObject;

class ImportRequest
{

    private $sourceId;
    private $sourceProviderId = 'default';
    private $importerId = null;
    private $createdBy = null;

    public static function createWithoutSource($importerId, $createdBy=null)
    {
        return new self(null, 'default', $importerId, $createdBy);
    }

    public function __construct($sourceId=null, $sourceProviderId='default', $importerId=null, $createdBy=null)
    {
        $this->sourceId = $sourceId;
        $this->sourceProviderId = $sourceProviderId;
        $this->importerId = $importerId;
        $this->createdBy = $createdBy;
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @return string|null
     */
    public function getSourceProviderId()
    {
        return $this->sourceProviderId;
    }

    /**
     * @return string|null
     */
    public function getImporterId()
    {
        return $this->importerId;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function hasImporterId()
    {
        return !empty($this->importerId);
    }

    public function hasSource()
    {
        return !empty($this->sourceId);
    }

}
