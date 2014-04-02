<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Mathielen\ImportEngine\Storage\DoctrineStorage;
use Doctrine\ORM\QueryBuilder;

class DoctrineQueryStorageProvider implements \IteratorAggregate, StorageProviderInterface
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

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
            $query = new \stdClass();

            if (is_string($classNameOrQuery)) {
                $query->name = $classNameOrQuery;
                $query->impl = $this->entityManager->createQueryBuilder()
                    ->select('o')
                    ->from($classNameOrQuery, 'o');
            } elseif ($classNameOrQuery instanceof QueryBuilder) {
                $query->impl = $classNameOrQuery;
                $query->name = $classNameOrQuery->getDQL();
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

    public function storage($id)
    {
        return new DoctrineStorage($id->impl);
    }
}
