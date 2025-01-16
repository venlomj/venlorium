<?php

namespace Lib\Core;

use Lib\Database\Database;
use Lib\HTTP\Router;
use Lib\Utilities\Config;




// Definieert de Kernel-klasse in de namespace App\Lib
class Kernel {
    // Definieert een instantie van de Database-klasse die in de Kernel wordt gebruikt
    public Database $db;

    private Router $router;

    // De run-methode die de applicatie uitvoert
    public function run() {
        // Laadt de configuratie-instellingen vanuit het .env-bestand
        Config::load();

        // Maakt verbinding met de database
        Database::connect();

        // Registreert de Router-klasse in de container onder de naam "router"
        // Dit betekent dat de Router-klasse beschikbaar zal zijn via de container,
        // zodat andere onderdelen van de applicatie toegang hebben tot de Router.
        // De Container::set() methode zorgt ervoor dat de Router-klasse kan worden geïnstantieerd en beheerd door de container.
        Container::set("router", Router::class);

        // Laadt de routes en voert de router uit
        // De loadRoutes-methode wordt aangeroepen om de gedefinieerde routes in de applicatie te laden
        // en de router uit te voeren om te bepalen welke route moet worden afgehandeld op basis van het verzoek.
        $this->loadRoutes();
   }

    // Nieuwe methode: Laadt de routes uit het api.php bestand
    // Het bestand 'api.php' bevat alle route-definities die de applicatie moet volgen.
    // De variabele $router wordt geïnitialiseerd met een instantie van de Router-klasse en
    // vervolgens wordt de 'run()' methode van de router aangeroepen om de route-afhandeling te starten.
    private function loadRoutes() {
        $router = require_once __DIR__ ."/../../routes.php";
        $this->router = $router;
        $this->router->run();
    }
}
