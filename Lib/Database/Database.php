<?php

namespace Lib\Database;

use Lib\Utilities\Config;


// Definieert de Database-klasse in de namespace App\Lib
class Database {
    // Statische PDO-verbinding die zal worden gebruikt door de applicatie
    public static \PDO $pdo;

    // Methode om verbinding te maken met de database
    public static function connect(): \PDO {
        // Haalt de configuratie-instellingen op via de Config-klasse
        $user = Config::get("DB_USER");    // Database-gebruiker
        $pass = Config::get("DB_PASSWORD"); // Database-wachtwoord
        $host = Config::get("DB_HOST");     // Database-host (bijvoorbeeld localhost)
        $port = Config::get("DB_PORT");     // Poort voor de databaseverbinding
        $db = Config::get("DB_NAME");       // Naam van de database

        try {
            // Bouwt de DSN-string voor de MySQL-verbinding met host, poort en database
            // Voeg de poort toe in de DSN-string
            self::$pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
            // Zet de PDO-attribute voor foutafhandeling naar 'exception' modus
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // Retourneer de gemaakte PDO-verbinding
            return self::$pdo; 
        } catch (\PDOException $e) {
            // Als er een fout optreedt, gooi een exception met de foutmelding
            throw new \Exception("Kan geen verbinding maken met de database: " . $e->getMessage());
        }
    }
}
