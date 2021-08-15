<?php

use App\Service\Database\MariaDbService;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MariaDbServiceTest extends TestCase
{
    private MariaDbService $mariaDbService;

    private MockObject $pdoMock;

    private MockObject $pdoStatementMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->pdoStatementMock = $this->createMock(PDOStatement::class);
        $this->pdoMock          = $this->createMock(PDO::class);
        $this->mariaDbService   = new MariaDbService($this->pdoMock);
    }

    public function testSelect()
    {
        $fields       = ['hals', 'nase', 'ohr'];
        $fieldsString = implode(',', $fields);
        $table        = 'gurke';
        $expected     = ['Hans', 'Wurst', 'Keks'];

        $this->pdoStatementMock->expects($this->once())
                               ->method('execute')
                               ->with(['lang', 'groß']);
        $this->pdoStatementMock->expects($this->once())
                               ->method('fetchAll')
                               ->with(new IsEmpty())
                               ->willReturn($expected);

        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->with("SELECT {$fieldsString} FROM {$table} WHERE nase = ? AND WHERE ohr = ?")
                      ->willReturn($this->pdoStatementMock);

        $actual = $this->mariaDbService->select($fields, $table, ['nase' => 'lang', 'ohr' => 'groß']);
        $this->assertSame($expected, $actual);
    }

    public function testInsert()
    {
        $table             = 'hasen';
        $fields            = ['hosen' => 'hasen', 'dosen' => 'rasen'];
        $fieldKeysString   = implode(',', array_keys($fields));
        $fieldValuesString = implode(',', array_fill(0, count($fields), '?'));

        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->with("INSERT INTO {$table} ({$fieldKeysString}) VALUES ({$fieldValuesString})")
                      ->willReturn($this->pdoStatementMock);

        $this->pdoStatementMock->expects($this->once())
                               ->method('execute')
                               ->with(array_values($fields));

        $this->mariaDbService->insert($table, $fields);
    }

    public function testUpdate()
    {
        $table      = 'heinz';
        $fields     = ['johnny' => 99, 'blüsen' => 6757];
        $conditions = ['hansi' => 66, 'nudel' => 'ääää'];
        $fieldsSql  = '';

        foreach (array_keys($fields) as $key) {
            $fieldsSql .= ($key === array_key_last($fields)) ? "{$key} = ?" : "{$key} = ?,";
        }

        $conditionsSql = (count($conditions) > 0) ? 'WHERE ' : '';

        foreach (array_keys($conditions) as $field) {
            $conditionsSql .= ($conditionsSql === 'WHERE ')
                ? "{$field} = ?"
                : " AND WHERE {$field} = ?";
        }

        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->with("UPDATE {$table} SET {$fieldsSql} {$conditionsSql}")
                      ->willReturn($this->pdoStatementMock);

        $this->pdoStatementMock->expects($this->once())
                               ->method('execute')
                               ->with(array_merge(array_values($fields), array_values($conditions)));

        $this->mariaDbService->update($table, $fields, $conditions);
    }

    public function testDelete()
    {
        $table         = 'jhsdf';
        $conditions    = ['ingo' => 'bingo', 'nasa' => 'feynman'];
        $conditionsSql = (count($conditions) > 0) ? 'WHERE ' : '';

        foreach (array_keys($conditions) as $field) {
            $conditionsSql .= ($conditionsSql === 'WHERE ')
                ? "{$field} = ?"
                : " AND WHERE {$field} = ?";
        }

        $this->pdoMock->expects($this->once())
                      ->method('prepare')
                      ->with("DELETE FROM {$table} {$conditionsSql}")
                      ->willReturn($this->pdoStatementMock);

        $this->pdoStatementMock->expects($this->once())
                               ->method('execute')
                               ->with(array_values($conditions));

        $this->mariaDbService->delete($table, $conditions);
    }
}