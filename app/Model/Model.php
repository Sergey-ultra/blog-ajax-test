<?php

declare(strict_types=1);

namespace App\Model;


use App\Core\Database\Connection;
use App\Core\Database\Traits\ForwardsCalls;
use App\Core\Exception\InsertException;


abstract class Model
{
    use ForwardsCalls;

    protected  $connection;
    protected string $table;

    protected $conn;


    public function __construct()
    {
        $this->conn = Connection::getConnection();
        $this->connection = $this->conn->db;
    }

    public  function getTableName(): string
    {
        return $this->table ?? (new \ReflectionClass(self::class))->getShortName();
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newModelQuery(), $method, $parameters);
    }


    public function newModelQuery()
    {
        return $this->newBaseQueryBuilder()
            ->setTable($this->getTableName());
    }


    /**
     * Get a new query builder instance for the connection.
     *
     * @return \App\Core\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->query();
    }

    /**
     * Get the database connection for the model.
     *
     * @return \App\Core\Database\Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    public function create(array $values): int
    {
        try {
            $columns = '(' . implode(', ', array_keys($values)) . ')';
            $args = $this->prepareArgumentsForInsert($values);

            $stmt = $this->connection->prepare("INSERT  INTO " . $this->getTableName() . " $columns VALUES $args");
            $stmt->execute(array_values($values));

            return (int) $this->connection->lastInsertId();

        } catch (\PDOException $e){
            throw new InsertException($e->getMessage());
        }
    }

    public function prepareArgumentsForInsert(array $values): string
    {
        return '(' . implode(',', array_fill(0, count($values), '?')) . ')';
    }


    protected function prepareArgumentsForBulkInsert(array $insertedInfo): string
    {
        $columnsCount = count($insertedInfo[0]);
        $rowsCount = count($insertedInfo);
        $length = $rowsCount * $columnsCount;

        /* Fill in chunks with '?' and separate them by group of $columnsCount */
        return implode(
            ',',
            array_map(
                function ($el) {
                    return '(' . implode(',', $el) . ')';
                },
                array_chunk(array_fill(0, $length, '?'), $columnsCount)
            )
        );
    }

    protected function getColumns(array $keys): string
    {

        return '(' .
            implode(
                ', ',
                array_map(
                    function($str): string {
                        return "`$str`";
                    },
                    $keys
                ),
            )
            . ')';
    }

    public function bulkInsert(array $insertedInfo): void
    {
        try {

            $columns = $this->getColumns(array_keys($insertedInfo[0]));
            $args = $this->prepareArgumentsForBulkInsert($insertedInfo);

            $stmt = $this->connection->prepare("INSERT  INTO " . $this->getTableName() . " $columns VALUES $args");

            $info = [];
            foreach ($insertedInfo as $insertRow) {
                $info = array_merge($info, array_values($insertRow));
            }

            $stmt->execute($info);
        } catch (\PDOException $e){
           throw new BulkInsertException($e->getMessage());
        }
    }

    public function insert(array $values): int
    {
        $columns = $this->getColumns($values);
        $args = implode(',', array_fill(0, count($values), '?'));

        $stmt = $this->connection->prepare("INSERT INTO ". $this->getTableName(). " $columns VALUES ($args)");
        $stmt->execute(array_values($values));

        return (int) $this->connection->lastInsertId();
    }
}