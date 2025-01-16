<?php

// Registreert een autoloader functie die automatisch klassenbestanden laadt wanneer een klasse wordt gebruikt.
// spl_autoload_register(function ($class) {
//     // Bepaalt het pad naar het bestand van de klasse:
//     // - `__DIR__` verwijst naar de directory van het huidige bestand.
//     // - `ucfirst` zorgt ervoor dat de eerste letter van de klasse met een hoofdletter begint.
//     // - `str_replace` vervangt backslashes in de naam van de namespace door backslashes in het pad (hier geen effect).
//     // - Voegt ".php" toe aan de bestandsnaam.
//     var_dump($class);
//     $path = __DIR__ . "/" . ucfirst(str_replace("\\", "/", $class)) . ".php";
//     // Debugging output
//     echo "Trying to load: $path\n";
//     // Laadt het bestand dat overeenkomt met de bepaalde klasse.
//     if (file_exists($path)) {
//         require_once $path;
//     } else {
//         throw new Exception("Class file for {$class} not found at path: {$path}");
//     }
// });

// Register an autoloader function that automatically loads class files when a class is used.
spl_autoload_register(function ($class) {
    // Define the base directories for different namespaces
    $baseDirs = [
        'App'  => __DIR__ . '../App/',   // User-defined logic
        'Lib'  => __DIR__ . '../Lib/',   // Core framework logic
    ];

    // Loop through each base directory to find the class file
    foreach ($baseDirs as $namespace => $baseDir) {
        // If the class starts with the current namespace
        if (strpos($class, $namespace) === 0) {
            // Strip the namespace prefix and replace backslashes with slashes
            $path = __DIR__ . "/" . ucfirst(str_replace("\\", "/", $class)) . ".php";

            // Debugging output
            //echo "Trying to load: $path\n";

            // If the file exists, include it
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    }

    // If no class file was found, throw an exception
    throw new Exception("Class file for {$class} not found.");
});




// Deze code zorgt ervoor dat wanneer een klasse wordt opgeroepen, het corresponderende bestand automatisch wordt geladen,
// zolang de naam van het bestand overeenkomt met de naam van de klasse en het bestand zich in de juiste directory bevindt.
