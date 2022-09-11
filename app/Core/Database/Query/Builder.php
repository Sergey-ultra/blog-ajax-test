<?php

declare(strict_types=1);


namespace App\Core\Database\Query;




use App\Model\Model;
use PDO;
use PDOStatement;

class Builder
{
    /**
     * The database connection instance.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The default fetch mode of the connection.
     *
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;


    protected $from;


    /**
     * The columns that should be returned.
     *
     * @var array
     */
    public $columns;

    /**
     * The current query value bindings.
     *
     * @var array
     */
    public $bindings = [
        'select' => [],
        'from' => [],
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'order' => [],
        'union' => [],
        'unionOrder' => [],
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }


    public function setTable(string $name)
    {
        $this->from($name);

        return $this;
    }


    public function from($table, $as = null)
    {
        $this->from = $as ? "{$table} as {$as}" : $table;

        return $this;
    }


    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return \App\Core\Database\Query\Builder
     */
    public function select($columns = ['*'])
    {
        $this->columns = [];
        $this->bindings['select'] = [];
        $columns = is_array($columns) ? $columns : func_get_args();

        $this->columns = $columns;

        return $this;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array|string  $columns
     * @return array
     */
    public function get($columns = ['*']): array
    {
        $statement = $this->prepared(
            $this->getPdo()->prepare($this->toSql())
        );

        $this->bindValues($statement, $this->bindings);

        $statement->execute();

        return $statement->fetchAll();

    }


    public function toSql(): string
    {
        $sql =  "SELECT " . implode(',', $this->columns) ."from " . $this->from;
        if ($this->where) {
            $sql .= " WHERE ";
        }
        return $sql;
    }

    protected function prepared(PDOStatement $statement): PDOStatement
    {
        $statement->setFetchMode($this->fetchMode);

        return $statement;
    }

    /**
     * Bind values to their parameters in the given statement.
     *
     * @param  \PDOStatement  $statement
     * @param  array  $bindings
     * @return void
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }
}