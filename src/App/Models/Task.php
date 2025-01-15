<?php 

namespace App\Models;

use PDO;

class Task
{
    public function getData(): array
    {
        $dsn = "mysql:host=localhost;dbname=tasksdb;port=2200";

        $pdo = new PDO($dsn,"root","1234", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $stmt = $pdo->query("SELECT * FROM tasks");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}