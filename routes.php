<?php

use Lib\Core\Container;




$router = Container::get("router");


$router->get("/api/auth/index", "AuthController@index");
$router->get("/api/auth/test/{name}", "AuthController@test");

return $router;
