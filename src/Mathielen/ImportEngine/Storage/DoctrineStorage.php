<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\DoctrineQueryReader;
use Doctrine\ORM\QueryBuilder;
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
     * @var QueryBuilder
     */
    private $queryBuilder;

    private $entityName;

    public function __construct(EntityManagerInterface $entityManager, $entityName=null, QueryBuilder $queryBuilder=null)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;

        if (is_null($queryBuilder)) {
            $queryBuilder = $this->entityManager->createQueryBuilder()
                ->select('o')
                ->from($this->entityName, 'o');
        }

        $this->queryBuilder = $queryBuilder;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        return new DoctrineQueryReader($this->queryBuilder);
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
            'name' => $this->queryBuilder->getDQL(),
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
