<?php

namespace Mathielen\ImportEngine\Storage\Provider\Connection;

use Mathielen\ImportEngine\ValueObject\StorageSelection;

class DefaultConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var array
     */
    private $connections;

    public function __construct(array $connections)
    {
        $this->connections = $connections;

        if (!isset($connections['default'])) {
            throw new \InvalidArgumentException("At least a 'default' connection must be given");
        }
    }

    public function addConnection($id, $connection)
    {
        $this->connections[$id] = $connection;
    }

    public function getConnection(StorageSelection $selection = null)
    {
        $id = $selection ? $selection->getId() : null;

        if (!$id || !isset($this->connections[$id])) {
            $id = 'default';
        }

        return $this->connections[$id];
    }
}
