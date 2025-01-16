<?php

namespace App\Lib;

// Definieert de BaseController-klasse die fungeert als basiscontroller voor andere controllers
class BaseController {
    /**
     * Constructor van de BaseController.
     * 
     * Deze constructor wordt automatisch uitgevoerd bij het aanmaken van een nieuwe instantie 
     * van een controller die deze klasse uitbreidt. Het zorgt ervoor dat inkomende verzoekgegevens 
     * (GET, POST, JSON) worden geparset en beschikbaar gemaakt via de Request-klasse.
     */
    public function __construct() {
        // Roept de parseIncoming-methode van de Request-klasse aan
        // Dit zorgt ervoor dat alle inkomende gegevens uit $_GET, $_POST en JSON 
        // correct worden geïnterpreteerd en opgeslagen in de Request-klasse.
        Request::parseIncoming();
    }
}
