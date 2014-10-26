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

    protected $createdBy;

    protected $finishedAt;

    protected $statistics;

    protected $info;

    /**
     * arbitrary data
     */
    protected $context;

    public function __construct(ImportConfiguration $configuration, $createdBy = null)
    {
        $this->id = uniqid();
        $this->createdAt = new \DateTime();
        $this->configuration = $configuration;
        $this->createdBy = $createdBy;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function isFinished()
    {
        return !empty($this->finishedAt);
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

    public function setInfo(array $info)
    {
        $this->info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'configuration' => $this->configuration?$this->configuration->toArray():null,
            'created_at' => $this->createdAt->getTimestamp(),
            'finished_at' => $this->finishedAt?$this->finishedAt->getTimestamp():null
        );
    }

}
