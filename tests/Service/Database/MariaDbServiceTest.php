<?php

use App\Service\Database\MariaDbService;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\TestCase;

class MariaDbServiceTest extends TestCase
{
    public function testSelect()
    {
        $fields       = ['hals', 'nase', 'ohr'];
        $fieldsString = implode(',', $fields);
        $table        = 'gurke';
        $expected     = ['Hans', 'Wurst', 'Keks'];

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                         ->method('execute')
                         ->with(['lang', 'groß']);
        $pdoStatementMock->expects($this->once())
                         ->method('fetchAll')
                         ->with(new IsEmpty())
                         ->willReturn($expected);

        $pdoMock = $this->createMock(PDO::Class);
        $pdoMock->expects($this->once())
                ->method('prepare')
                ->with("SELECT {$fieldsString} FROM {$table} WHERE nase = ? AND WHERE ohr = ?")
                ->willReturn($pdoStatementMock);

        $mariaDbService = new MariaDbService($pdoMock);

        $actual = $mariaDbService->select($fields, $table, ['nase' => 'lang', 'ohr' => 'groß']);
        $this->assertSame($expected, $actual);
    }
}