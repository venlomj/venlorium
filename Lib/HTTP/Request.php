<?php

namespace Lib\HTTP;

// Definieert de Request-klasse voor het beheren en verwerken van HTTP-verzoeken
class Request {
    // Eigenschap die de gegevens van het verzoekslichaam (body) opslaat
    private static array $body = [];
    // Eigenschap die de queryparameters van het verzoek opslaat
    private static array $query = [];

    // Methode om een specifieke waarde uit het verzoekslichaam op te halen
    // - `$key`: De sleutel van de waarde die wordt gezocht.
    // - `$default`: Een standaardwaarde die wordt geretourneerd als de sleutel niet bestaat.
    public static function body(string $key = null, $default = null)
    {
        return self::$body[$key] ?? $default; // Retourneert de waarde of de standaardwaarde.
    }

    // Methode om een specifieke queryparameter op te halen
    // - `$key`: De sleutel van de parameter die wordt gezocht.
    // - `$default`: Een standaardwaarde die wordt geretourneerd als de sleutel niet bestaat.
    public static function query(string $key = null, $default = null){
        return self::$query[$key] ?? $default; // Retourneert de waarde of de standaardwaarde.
    }

    // Methode om de inkomende verzoekgegevens te parsen
    // Deze methode vult de `$query`- en `$body`-eigenschappen met gegevens uit respectievelijk `$_GET` en `$_POST`.
    // Daarnaast verwerkt het ook JSON-verzoeken en voegt deze toe aan de `$body`.
    public static function parseIncoming(){
        // Verwerk de queryparameters uit `$_GET` en sla ze op in `$query`.
        foreach ($_GET as $key => $value) {
            self::$query[$key] = $value;
        }

        // Verwerk de gegevens van het verzoekslichaam uit `$_POST` en sla ze op in `$body`.
        foreach ($_POST as $key => $value) {
            self::$body[$key] = $value;
        }

        // Verwerk eventuele JSON-gegevens uit het verzoekslichaam.
        $json = file_get_contents("php://input");
        if ($json) {
            // Decodeer de JSON-gegevens naar een associatieve array.
            $json = json_decode($json, true);
            // Voeg de JSON-gegevens toe aan `$body`.
            foreach ($json as $key => $value) {
                self::$body[$key] = $value;
            }
        }
    }
}
