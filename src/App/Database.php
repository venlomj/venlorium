<?php

namespace App;

use PDO;

class Database
{
    private ?PDO $pdo = null;
    public function __construct(private string $host,
                                private string $name,
                                private string $username,
                                private string $password,
                                private string $port)
    {}
    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->name};port={$this->port}";

        $this->pdo = new PDO($dsn,$this->username,$this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        }
        return $this->pdo;
    }
}