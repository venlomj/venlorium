<?php

use Lib\Core\Container;

$router = Container::get("router");

$router->get("/api/auth/index", "AuthController@index");
//$router->get("/api/auth/test/{name}", "AuthController@test");
$router->get("/api/auth/find/{id}", "AuthController@find");
$router->post("/api/auth/create", "AuthController@create");

return $router;

