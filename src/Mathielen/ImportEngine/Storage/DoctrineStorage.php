<?php
namespace Mathielen\ImportEngine\Storage;

use Doctrine\ORM\Query;
use Mathielen\DataImport\Reader\DoctrineQueryReader;
use Doctrine\ORM\EntityManagerInterface;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class DoctrineStorage implements StorageInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Query
     */
    private $query;

    private $entityName;

    public function __construct(EntityManagerInterface $entityManager, $entityName=null, Query $query=null)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;

        if (is_null($query) && is_string($entityName) && class_exists($entityName)) {
            $query = $this->entityManager->createQueryBuilder()
                ->select('o')
                ->from($this->entityName, 'o')
                ->getQuery();
        }

        $this->query = $query;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        return new DoctrineQueryReader($this->query);
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        if (empty($this->entityName)) {
            throw new InvalidConfigurationException("Can only use doctrine for writing if entityName is given.");
        }

        return new DoctrineWriter($this->entityManager, $this->entityName);
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        $count = count($this->reader());

        return new StorageInfo(array(
            'name' => $this->query->getDQL(),
            'type' => 'DQL Query',
            'count' => $count
        ));
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        return $this->reader()->getFields();
    }
}
