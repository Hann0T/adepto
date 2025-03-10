<?php

namespace Adepto;

class Database
{
    public function __construct(public \PDO $db)
    {
        //
    }

    // this can be a general function so insert only calls this?
    public function statement(string $query): mixed
    {
        return $this->db->exec($query);
    }

    public function insert(string $query, array $params): mixed
    {
        $statement = $this->db->prepare($query);
        return $statement->execute($params);
    }

    public function select(string $query, array $params): mixed
    {
        $statement = $this->db->prepare($query);
        $statement->execute($params);
        return $statement->fetch();
    }

    public function delete(string $query): mixed
    {
        return $this->statement($query);
    }
}
