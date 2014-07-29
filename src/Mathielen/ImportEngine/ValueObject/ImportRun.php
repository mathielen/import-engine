<?php
namespace Mathielen\ImportEngine\ValueObject;

class ImportRun
{

    protected $id;

    /**
     * @var ImportConfiguration
     */
    protected $configuration;

    protected $createdAt;

    protected $finishedAt;

    protected $statistics;

    public function __construct(ImportConfiguration $configuration)
    {
        $this->id = uniqid();
        $this->createdAt = new \DateTime();
        $this->configuration = $configuration;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isFinished()
    {
        return false;
    }

    /**
     * @return ImportConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function finish()
    {
        $this->finishedAt = new \DateTime();
    }

    public function setStatistics(array $statistics)
    {
        $this->statistics = $statistics;

        return $this;
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'configuration' => $this->configuration->toArray(),
            'created_at' => $this->createdAt->getTimestamp(),
            'finished_at' => $this->finishedAt?$this->finishedAt->getTimestamp():null
        );
    }

}
