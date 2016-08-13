<?php

namespace Mathielen\ImportEngine\Storage\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mathielen\ImportEngine\Storage\Provider\Connection\ConnectionFactoryInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Storage\DoctrineStorage;

class DoctrineQueryStorageProvider implements \IteratorAggregate, StorageProviderInterface
{
    /**
     * @var ConnectionFactoryInterface
     */
    private $connectionFactory;

    /**
     * @var QueryBuilder[]
     */
    private $queries;

    public function __construct(ConnectionFactoryInterface $connectionFactory, $classNamesOrQueries)
    {
        $this->connectionFactory = $connectionFactory;
        $this->resolveQueries($classNamesOrQueries);
    }

    private function resolveQueries($classNamesOrQueries)
    {
        if (!is_array($classNamesOrQueries) && !$classNamesOrQueries instanceof \Traversable) {
            throw new \InvalidArgumentException('classNamesOrQueries must be an array or Traversable');
        }

        //TODO what about different entity providers ?
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->connectionFactory->getConnection();

        $this->queries = array();
        foreach ($classNamesOrQueries as $k => $classNameOrQuery) {
            if (is_string($classNameOrQuery) && class_exists($classNameOrQuery)) {
                $query = $entityManager->createQueryBuilder()
                    ->select('o')
                    ->from($classNameOrQuery, 'o')
                    ->getQuery();
                $selection = new StorageSelection($query, $classNameOrQuery, $classNameOrQuery);
            } elseif (is_string($classNameOrQuery)) {
                $query = $entityManager->createQuery($classNameOrQuery);
                $selection = new StorageSelection($query, $query->getDQL(), $query->getDQL());
            } elseif ($classNameOrQuery instanceof Query) {
                $selection = new StorageSelection($classNameOrQuery, $classNameOrQuery->getDQL(), $classNameOrQuery->getDQL());
            } else {
                throw new \InvalidArgumentException('Only strings or QueryBuilder are allowed!');
            }

            $this->queries[$k] = $selection;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->queries);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        $connection = $this->connectionFactory->getConnection($selection);

        return new DoctrineStorage($connection, $selection->getName(), $selection->getImpl());
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        if ($id instanceof StorageSelection) {
            return $id;
        } else {
            return $this->queries[$id];
        }
    }
}
