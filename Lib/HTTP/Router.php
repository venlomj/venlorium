<?php

namespace App\Lib;

// Definieert de Router-klasse in de namespace App\Lib
class Router {
    // Een array die alle routes van de applicatie bevat
    private array $routes = [];

    // Huidige route die momenteel wordt uitgevoerd
    private ?string $current_route = null;

    // Vorige route die is uitgevoerd
    private ?string $prev_route = null;

    // Methode om een nieuwe route toe te voegen
    public function add(string $route, string $method, string|array|\Closure $callback) {
        // Voegt een route toe aan de routes-array met de bijbehorende callback en HTTP-methode
        $this->routes[$route] = [
            "callback" => $callback,
            "method" => $method
        ];

        // Slaat de huidige route op
        $this->current_route = $route;
        return $this;
    }

    // Voeg een GET-route toe
    public function get(string $route, string|array|\Closure $callback) {
        $this->add($route, "get", $callback);
    }


    // Voeg een POST-route toe
    public function post(string $route, $callback) {
        $this->add($route, "post", $callback);
    }

    // Methode om middleware toe te voegen voor de huidige route
    public function middleware(...$args) {
        if ($this->current_route) {
            // Voeg de middleware toe aan de huidige route
            $this->routes[$this->current_route]["middleware"][] = $args;
        }
        return $this;
    }

    // Methode om de juiste route en callback uit te voeren
    public function run() {
        // Haalt de huidige URI van de server op
        $route = $_SERVER["REQUEST_URI"];
        // Verwijdert eventuele querystring van de route
        $route = explode("?", $route)[0];

        // Splitst de route in delen gebaseerd op "/"
        $parts = explode("/", $route);
        array_shift($parts);

        // Als de route geen onderdelen bevat, voeg een lege string toe
        if (!count($parts)) {
           $parts[] = ""; 
        }

        // Variabelen om de huidige route, callback, methode en parameters bij te houden
        $current_route = null;
        $current_callback = null;
        $current_method = null;
        $current_params = [];

        // Itereer door alle gedefinieerde routes
        foreach ($this->routes as $route => $data) {
            // Splitst de route in delen
            $route_parts = explode("/", $route);
            array_shift($route_parts);

            // Vergelijk de onderdelen van de huidige route met de opgegeven route
            for ($i = 0; $i < count($route_parts); $i++) {
                // Zoekt naar routeparameters gemarkeerd met {parameter}
                preg_match("/{(.*)}/", $route_parts[$i], $matches);
                for ($j = 0; $j < count($parts); $j++) {
                    if ($i == $j) {
                        // Als de route en het pad overeenkomen, sla dan de gegevens op
                        if ($route_parts[$i] == $parts[$j]) {
                            $current_route = $route;
                            $current_callback = $data["callback"];
                            $current_method = $data["method"];
                            $this->prev_route = $route;
                            continue;
                        // Als de routeparameter een wildcard is, sla deze dan op als een parameter
                        } elseif(!empty($matches[1]) && $this->prev_route == $route) {
                            $current_route = $route;
                            $current_callback = $data["callback"];
                            $current_method = $data["method"];
                            $current_params[] = $parts[$j];
                            continue;
                        } else {
                            $current_route = null;
                            break;
                        }
                    } else {
                        $current_route = null;
                        continue;
                    }
                }
            }

            // Als een overeenkomstige route is gevonden, stop met zoeken
            if ($current_route) {
                break;
            }
        }

        // Controleer of er middleware is gedefinieerd voor de gevonden route
        if (isset($this->routes[$current_route]["middleware"])) {
            foreach ($this->routes[$current_route]["middleware"] as $middleware) {
                // Maak een instantie van de middleware en voer de handle-methode uit
                $middleware = new $middleware;
                $middleware->handle();
            }
        }

        // Als er een callback en route zijn gevonden
        if ($current_callback && $current_route) {
            // Controleer of de HTTP-methode overeenkomt met de verwachte methode voor deze route
            if (!strtolower($_SERVER["REQUEST_METHOD"]) == $current_method) {
                exit("Method not allowed");
            }

            // Splitst de callback in klasse en methode
            $class_parts = explode("@", $current_callback);
            $class = $class_parts[0];
            $method = $class_parts[1];
            // Bouwt de volledige naam van de controllerklasse
            $class_name = "\\App\\Controllers\\" . $class;
            // Zet de controller in de container
            Container::set($class_name, $class_name);
            // Haal de controller uit de container en voer de methode uit met de parameters
            $class = Container::get($class_name);
            $response = $class->$method(...$current_params);
            if (!empty($response)) {
                Response::json($response);
            }
        } else {
            // Als er geen overeenkomstige route wordt gevonden, toon een 404-foutmelding
            echo "404";
        }
    }
}
