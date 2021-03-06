<?php

namespace App\Service\Database;

use PDO;

class MariaDbService implements RelationalDatabaseInterface
{
    private PDO $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function select(array $fields, string $table, array $conditions = []): array
    {
        $fieldsSql     = implode(',', $fields);
        $conditionsSql = $this->buildConditionsSQL($conditions);

        $statement = $this->pdo->prepare("SELECT {$fieldsSql} FROM {$table} {$conditionsSql}");
        $statement->execute(array_values($conditions));

        return $statement->fetchAll();
    }

    public function insert(string $table, array $fields): void
    {
        $fieldsSql = implode(',', array_keys($fields));
        $valuesSql = implode(',', array_fill(0, count($fields), '?'));

        $statement = $this->pdo->prepare("INSERT INTO {$table} ({$fieldsSql}) VALUES ({$valuesSql})");
        $statement->execute(array_values($fields));
    }

    public function update(string $table, array $fields, array $conditions): void
    {
        $fieldsSql = '';

        foreach (array_keys($fields) as $key) {
            $fieldsSql .= ($key === array_key_last($fields)) ? "{$key} = ?" : "{$key} = ?,";
        }

        $conditionsSql = $this->buildConditionsSQL($conditions);

        $statement = $this->pdo->prepare("UPDATE {$table} SET {$fieldsSql} {$conditionsSql}");
        $statement->execute(array_merge(array_values($fields), array_values($conditions)));
    }

    public function delete(string $table, array $conditions): void
    {
        $conditionsSql = $this->buildConditionsSQL($conditions);

        $statement = $this->pdo->prepare("DELETE FROM {$table} {$conditionsSql}");
        $statement->execute(array_values($conditions));
    }

    private function buildConditionsSQL(array $conditions): string
    {
        if(count($conditions) === 0) {
            return '';
        }

        $conditionsSql =  'WHERE ';

        foreach (array_keys($conditions) as $field) {
            $conditionsSql .= ($conditionsSql === 'WHERE ')
                ? "{$field} = ?"
                : " AND WHERE {$field} = ?";
        }

        return $conditionsSql;
    }
}