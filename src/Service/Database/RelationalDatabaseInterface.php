<?php

namespace App\Service\Database;

interface RelationalDatabaseInterface
{
    public function select($fields, string $table, array $conditions = []): array;

    public function insert(string $table, array $fields): void;

    public function update(string $table, array $fields, array $conditions): void;

    public function delete(string $string, array $conditions): void;
}