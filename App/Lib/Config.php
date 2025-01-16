<?php

namespace App\Lib;

// Definieert een klasse genaamd Config in de namespace App\Lib.
class Config {
    // Methode om configuratie-instellingen te laden vanuit een .env-bestand.
    public static function load() {
        // Bepaalt het pad naar het .env-bestand dat zich één niveau hoger bevindt dan de huidige directory.
        $path = __DIR__ . "/../.env";

        // Leest de inhoud van het .env-bestand regel voor regel:
        // - `FILE_IGNORE_NEW_LINES`: Haalt de nieuwe regelkarakters aan het einde van regels weg.
        // - `FILE_SKIP_EMPTY_LINES`: Slaat lege regels over.
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Itereert door alle regels in het .env-bestand.
        foreach ($lines as $line) {
            // Als de regel begint met een `#`, wordt deze als een commentaar beschouwd en overgeslagen.
            if (strpos($line, "#") === 0) {
                continue;
            }
            // Splitst de regel in een sleutel en waarde rond het eerste `#`-teken.
            list($name, $value) = explode("=", $line, 2);
            $name = trim($name);   // Verwijdert eventuele spaties rond de sleutel.
            $value = trim($value); // Verwijdert eventuele spaties rond de waarde.

            // Als de sleutel nog niet in de $_ENV-array staat, wordt deze toegevoegd:
            if (!array_key_exists($name, $_ENV)) {
                // Stelt de omgevingsvariabele in via `putenv` (key=value).
                putenv(sprintf("%s=%s", $name, $value));
                // Voegt de sleutel en waarde toe aan de globale $_ENV-array.
                $_ENV[$name] = $value;
            }
        }
    }

    public static function get(string $key) {
         // Geeft de waarde terug van een sleutel in de configuratie.
        // Retourneert `null` als de sleutel niet bestaat.
        return $_ENV[$key] ?? null;
    }
}
