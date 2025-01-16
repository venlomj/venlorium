<?php

namespace Lib\Core;

// Definieert een klasse genaamd Container in de namespace App\Lib.
// Deze klasse fungeert als een eenvoudige dependency injection container.
class Container {
    // Array om de geregistreerde services en hun instanties op te slaan.
    private static $services = [];

    // Methode om een service op te halen uit de container.
    // - `$key` is de naam van de service.
    public static function get(string $key) {
        // Geeft de instantie van de service terug die hoort bij de opgegeven sleutel.
        return self::$services[$key];
    }

    // Methode om een service toe te voegen aan de container.
    // - `$key` is de naam van de service.
    // - `$value` kan een string (klassenaam) of een bestaand object zijn.
    public static function set(string $key, $value) {
        // Als de waarde een string is (klassenaam), maak een nieuwe instantie van die klasse.
        if (is_string($value)) {
            self::$services[$key] = new $value();
        // Als de waarde een object is, sla het object direct op in de container.
        } elseif (is_object($value)) {
            self::$services[$key] = $value;
        }
    }
}
