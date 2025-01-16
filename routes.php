<?php

use App\Controllers\AuthController;
use App\Lib\Container;

$router = Container::get("router");


$router->get("/api/auth/index", "AuthController@index");
$router->get("/api/auth/test/{name}", "AuthController@test");

return $router;
