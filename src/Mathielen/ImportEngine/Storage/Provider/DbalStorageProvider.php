<?php

namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\DbalStorage;
use Mathielen\ImportEngine\Storage\Provider\Connection\ConnectionFactoryInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class DbalStorageProvider implements StorageProviderInterface
{
    /**
     * @var ConnectionFactoryInterface
     */
    private $connectionFactory;

    /**
     * @var \ArrayAccess
     */
    private $queries;

    public function __construct(ConnectionFactoryInterface $connectionFactory, \ArrayAccess $queries)
    {
        $this->connectionFactory = $connectionFactory;
        $this->queries = $queries;
    }

    /**
     * @return DbalStorage
     */
    public function storage(StorageSelection $selection)
    {
        $connection = $this->connectionFactory->getConnection($selection);

        return new DbalStorage($connection, $selection->getName(), $selection->getImpl());
    }

    /**
     * @return StorageSelection
     */
    public function select($id = null)
    {
        if (!$id) {
            throw new \InvalidArgumentException('id must not be empty');
        }

        return new StorageSelection($this->queries[$id], $id, $id);
    }
}
