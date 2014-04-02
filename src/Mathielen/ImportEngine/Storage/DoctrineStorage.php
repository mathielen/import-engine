<?php
namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\DoctrineQueryReader;
use Doctrine\ORM\QueryBuilder;

class DoctrineStorage implements StorageInterface
{

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
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
        // TODO: Auto-generated method stub
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
        //TODO what if write?
        return $this->reader()->getFields();
    }
}
