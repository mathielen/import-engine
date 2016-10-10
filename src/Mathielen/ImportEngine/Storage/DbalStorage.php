<?php

namespace Mathielen\ImportEngine\Storage;

use Mathielen\DataImport\Reader\DbalReader;
use Doctrine\DBAL\Connection;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

class DbalStorage implements StorageInterface
{
    /**
     * @var Connection
     */
    private $connection;

    private $tableName;

    private $query;

    private $info;

    public function __construct(Connection $connection, $tableName = null, $query = null)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->query = $query;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param null $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param null $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer().
     */
    public function writer()
    {
        if (empty($this->tableName)) {
            throw new InvalidConfigurationException('Can only use pdo for writing if tableName is given.');
        }

        //TODO implement me

        //return $writer;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info().
     */
    public function info()
    {
        if (!$this->info) {
            $this->info = new StorageInfo(array(
                'name' => $this->query,
                'type' => 'SQL Query',
                'count' => count($this->reader()),
            ));
        }

        return $this->info;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader().
     */
    public function reader()
    {
        return new DbalReader($this->connection, $this->query);
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields().
     */
    public function getFields()
    {
        return $this->reader()->getFields();
    }
}
