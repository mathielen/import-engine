<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;
use Mathielen\ImportEngine\Storage\DoctrineStorage;

class DoctrineQueryStorageProvider implements \IteratorAggregate, StorageProviderInterface
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var QueryBuilder[]
     */
    private $queries;

    public function __construct(EntityManagerInterface $entityManager, array $classNamesOrQueries)
    {
        $this->entityManager = $entityManager;
        $this->resolveQueries($classNamesOrQueries);
    }

    private function resolveQueries(array $classNamesOrQueries)
    {
        $this->queries = array();
        foreach ($classNamesOrQueries as &$classNameOrQuery) {
            if (is_string($classNameOrQuery)) {
                $queryBuilder = $this->entityManager->createQueryBuilder()
                    ->select('o')
                    ->from($classNameOrQuery, 'o');
                $query = new StorageSelection($classNameOrQuery, $classNameOrQuery, $queryBuilder);

            } elseif ($classNameOrQuery instanceof QueryBuilder) {
                $query = new StorageSelection($classNameOrQuery->getDQL(), $classNameOrQuery->getDQL(), $classNameOrQuery);

            } else {
                throw new \InvalidArgumentException("Only strings or QueryBuilder are allowed!");
            }

            $this->queries[] = $query;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->queries);
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::storage()
     */
    public function storage(StorageSelection $selection)
    {
        return new DoctrineStorage($this->entityManager, $selection->getImpl());
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id)
    {
        return $id;
    }

}
