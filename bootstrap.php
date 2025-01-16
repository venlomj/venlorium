<?php

// Registreert een autoloader functie die automatisch klassenbestanden laadt wanneer een klasse wordt gebruikt.
spl_autoload_register(function ($class) {
    // Bepaalt het pad naar het bestand van de klasse:
    // - `__DIR__` verwijst naar de directory van het huidige bestand.
    // - `ucfirst` zorgt ervoor dat de eerste letter van de klasse met een hoofdletter begint.
    // - `str_replace` vervangt backslashes in de naam van de namespace door backslashes in het pad (hier geen effect).
    // - Voegt ".php" toe aan de bestandsnaam.
    $path = __DIR__ . "/../" . ucfirst(str_replace("\\", "\\", $class)) . ".php";
    
    // Laadt het bestand dat overeenkomt met de bepaalde klasse.
    require_once $path;
});

// Deze code zorgt ervoor dat wanneer een klasse wordt opgeroepen, het corresponderende bestand automatisch wordt geladen,
// zolang de naam van het bestand overeenkomt met de naam van de klasse en het bestand zich in de juiste directory bevindt.
