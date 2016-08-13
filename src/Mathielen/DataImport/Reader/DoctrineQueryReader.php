<?php

namespace Mathielen\DataImport\Reader;

use Ddeboer\DataImport\Reader\DoctrineReader;
use Doctrine\ORM\Query;

/**
 * Reads entities through the Doctrine ORM via a definied query.
 */
class DoctrineQueryReader extends DoctrineReader
{
    /**
     * @var Query
     */
    private $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        //TODO this totally sucks. Is there a better way?
        $firstResult = $this->query->iterate(array(), Query::HYDRATE_ARRAY);
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
            $this->iterableResult = $this->query->iterate(array(), Query::HYDRATE_ARRAY);
        }

        $this->iterableResult->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($this->query);
        $totalRows = count($paginator);

        return $totalRows;
    }
}
