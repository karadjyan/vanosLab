<?php


class QueryBuilder
{
    /** @var PDO */
    private $connection;
    private $select;
    private $from;
    private $join;
    private $where;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->select = [];
        $this->from = '';
        $this->join = [];
        $this->where = [];
    }

    public function select($column)
    {
        $this->select[] = $column;

        return $this;
    }

    public function from($from) {
        $this->from = $from;

        return $this;
    }

    public function join($table, $first, $operator, $second)
    {
        $this->join[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
        ];

        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    public function when($condition, $closure)
    {
        if ($condition) {
            $closure($this);
        }

        return $this;
    }

    private function getWhereStatements()
    {
        $whereStatements = [];

        foreach ($this->where as $index => $condition) {
            $whereStatements[] = [
                'stmt' => $condition['column'] . ' ' . $condition['operator'] . ' ' . ':whereValue' . $index,
                'bind' => [
                    'whereValue' . $index => $condition['value'],
                ]
            ];
        }

        return $whereStatements;
    }

    private function getJoinStatements()
    {
        $joinStatements = [];

        foreach ($this->join as $index => $condition) {
            $joinStatements[] = [
                'stmt' => ' INNER JOIN ' .  $condition['table'] .
                    ' ON ' .$condition['first'] . ' ' . $condition['operator'] . ' ' . $condition['second'],
            ];
        }

        return $joinStatements;
    }

    public function get()
    {
        $sqlString = 'SELECT ' . implode(', ', $this->select) . ' FROM ' . $this->from;
        $bindings = [];

        $joinStmt = $this->getJoinStatements();

        if (!empty($joinStmt)) {
            foreach ($joinStmt as $join) {
                $sqlString .= $join['stmt'];
            }
        }

        $whereStmt = $this->getWhereStatements();

        if (!empty($whereStmt)) {
            $sqlString .= ' WHERE ';

            $sqlString .= implode(' AND ', array_column($whereStmt, 'stmt'));
            $bindings = array_merge($bindings, ...array_column($whereStmt, 'bind'));
        }

        $stmt = $this->connection->prepare($sqlString);


        foreach ($bindings as $key => $binding) {
            $stmt->bindParam(':' . $key, $binding);
        }

        return $stmt;
    }
}
