<?php

namespace Lib\HTTP;

// Definieert de Response-klasse voor het beheren en verzenden van HTTP-responsen
class Response {
    /**
     * Stuurt een JSON-response naar de client.
     * 
     * @param array $data   De gegevens die als JSON worden geretourneerd.
     * @param int   $status De HTTP-statuscode van de respons (standaard: 200).
     */
    public static function json(array $data, int $status = 200) {
        // Stel de Content-Type-header in om aan te geven dat het antwoord JSON is
        header("Content-Type: application/json");
        
        // Stel de HTTP-statuscode in van de respons
        http_response_code($status);
        
        // Encodeer de data-array naar JSON en stuur deze naar de client
        echo json_encode($data);
        
        // Stop verdere scriptuitvoering na het verzenden van de respons
        exit();
    }
}
