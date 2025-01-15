<?php

namespace App;

use PDO;

class Database
{
    public function getConnection(): PDO
    {
        $dsn = "mysql:host=localhost;dbname=tasksdb;port=2200";

        return new PDO($dsn,"root","1234", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }
}