<?php

declare(strict_types= 1);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

spl_autoload_register(function (string $class_name) {

    require "src/" . str_replace("\\", "/", $class_name) . ".php";

});

$router = new Framework\Router;

$router->add("/admin/{controller}/{action}", ["namespace" => "Admin"]);
$router->add("/{title}/{id:\d+}/{page:\d+}", ["controller" => "tasks","action"=> "showPage"]);
$router->add("/task/{slug:[\w-]+}", ["controller" => "tasks", "action" => "show"]);
$router->add("/{controller}/{id:\d+}/{action}");
$router->add("/home/index", ["controller" => "home", "action" => "index"]);
$router->add("/tasks", ["controller" => "tasks", "action" => "index"]);
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/{controller}/{action}");

$container = new Framework\Container;


$container->set(App\Database::class, function() {
    return new App\Database("localhost", "tasksdb", "root", "1234", "2200");
});

$dispatcher = new Framework\Dispatcher($router, $container); 

$dispatcher->handle($path);