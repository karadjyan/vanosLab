<?php


class Database
{
    private $connection;

    public function __construct($user, $pass)
    {
        $this->connection = ;
    }

    public function insert($table, $data)
    {
        if (!is_array($data)) {
            return false;
        }

        $keys = array_keys($data);
        $values = array_values($data);
        $prepareValues = implode(', ',  array_pad([], count($values), '?'));

        $stmt = $this->connection->prepare(
            'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . $prepareValues .')'
        );

        foreach ($values as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        return $stmt->execute();
    }

    public function fetch()
    {
        
    }
}