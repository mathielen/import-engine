<?php
namespace Mathielen\ImportEngine\Storage;

use Ddeboer\DataImport\Reader\DbalReader;
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

    public function __construct(Connection $connection, $tableName=null, $query=null)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->query = $query;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::writer()
     */
    public function writer()
    {
        if (empty($this->tableName)) {
            throw new InvalidConfigurationException("Can only use pdo for writing if tableName is given.");
        }

        //TODO implement me

        //return $writer;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::info()
     */
    public function info()
    {
        if (!$this->info) {
            $this->info = new StorageInfo(array(
                'name' => $this->query,
                'type' => 'SQL Query',
                'count' => count($this->reader())
            ));
        }

        return $this->info;
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::reader()
     */
    public function reader()
    {
        return new DbalReader($this->connection, $this->query);
    }

    /**
     * (non-PHPdoc) @see \Mathielen\ImportEngine\Storage\StorageInterface::getFields()
     */
    public function getFields()
    {
        return $this->reader()->getFields();
    }
}
