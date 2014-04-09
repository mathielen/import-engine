<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\DoctrineQueryReader;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Ddeboer\DataImport\Writer\DoctrineWriter;

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

    public function __construct(EntityManagerInterface $entityManager, $queryBuilderOrEntityName)
    {
        $this->entityManager = $entityManager;

        if (is_string($queryBuilderOrEntityName)) {
            $queryBuilder = $this->entityManager->createQueryBuilder()
                ->select('o')
                ->from($queryBuilderOrEntityName, 'o');
            $entityName = $queryBuilderOrEntityName;

        } elseif ($queryBuilderOrEntityName instanceof QueryBuilder) {
            $queryBuilder = $queryBuilderOrEntityName;
            //TODO entityName??

        } else {
            throw new \InvalidArgumentException("Only strings or QueryBuilder are allowed!");
        }

        $this->entityName = $entityName;
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
        return new DoctrineWriter($this->entityManager, $this->entityName);
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        $count = count($this->reader());

        return array(
            'name' => $this->queryBuilder->getDQL(),
            'type' => 'DQL Query',
            'count' => $count
        );
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        return $this->entityManager->getClassMetadata($this->entityName)
                 ->getFieldNames();
    }
}
