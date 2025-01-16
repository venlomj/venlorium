<?php

namespace Lib\Utilities;

// Definieert de Collection-klasse die het JsonSerializable-interface implementeert
// Dit maakt het mogelijk om een Collection-klasse eenvoudig te serialiseren naar JSON.
class Collection implements \JsonSerializable {
    // Beschermde eigenschap $items die een array bevat met de items in de collectie
    protected array $items = [];

    // Constructor die een array van items als parameter accepteert
    // De ontvangen items worden opgeslagen in de eigenschap $items.
    public function __construct(array $items = []) 
    {
        $this->items = $items; // Initialiseer de items in de collectie
    }

    // Methode om een callback toe te passen op elk item in de collectie
    // `map()` neemt een callable als parameter, voert deze uit op elk item en retourneert een nieuwe array met de resultaten.
    public function map(callable $callback) {
        return array_map($callback, $this->items);
    }

    // Methode om de items in de collectie te filteren op basis van een callback
    // `filter()` neemt een callable als parameter en retourneert een nieuwe array met alleen de items waarvoor de callback true retourneert.
    public function filter(callable $callback) {
        return array_filter($this->items, $callback);
    }

    public function toArray(): array {
        return $this->map(callback: function($item) {
             return $item->toArray();
            });
    }

    // Methode die wordt aangeroepen wanneer een instantie van de Collection naar JSON wordt geserialiseerd
    // Het retourneert de items in de collectie, zodat deze correct in JSON-formaat worden weergegeven.
    public function jsonSerialize(): mixed
    {
        return $this->items;
    }

    public function isEmpty(): bool {
        return empty($this->items);
    }    
}
