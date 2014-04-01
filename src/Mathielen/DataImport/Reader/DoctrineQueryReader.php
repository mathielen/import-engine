<?php
namespace Mathielen\DataImport\Reader;

use Ddeboer\DataImport\Reader\DoctrineReader;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Reads entities through the Doctrine ORM via a definied query
 */
class DoctrineQueryReader extends DoctrineReader
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
     * {@inheritdoc}
     */
    public function getFields()
    {
        //TODO this totally sucks. Is there a better way?
        $firstResult = $this->queryBuilder->getQuery()->iterate(array(), Query::HYDRATE_ARRAY);
        $firstResult->rewind();
        $row = $firstResult->current();

        return array_keys($row[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if (!$this->iterableResult) {
            $this->iterableResult = $this->queryBuilder->getQuery()->iterate(array(), Query::HYDRATE_ARRAY);
        }

        $this->iterableResult->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $qb = clone $this->queryBuilder;
        $q = $qb
            ->select("count(o)")
            ->getQuery();

        return $q->getSingleScalarResult();
    }

}
